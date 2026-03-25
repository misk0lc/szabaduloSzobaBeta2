<?php

namespace App\Http\Controllers;

use App\Models\MultiplayerSession;
use App\Models\Question;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MultiplayerController extends Controller
{
    /**
     * POST /api/multiplayer/join
     * Csatlakozás vagy új session létrehozása egy szobához.
     * Ha van várakozó session → csatlakozik, status → playing
     * Ha nincs → új waiting session
     */
    public function join(Request $request)
    {
        $request->validate(['level_id' => 'required|integer']);
        $levelId = $request->input('level_id');
        $user    = $request->user();

        // Ha már benne van egy aktív sessionben ennél a szobánál, visszaadjuk azt
        $existing = MultiplayerSession::where('LevelID', $levelId)
            ->whereIn('Status', ['waiting', 'playing'])
            ->whereHas('users', fn($q) => $q->where('multiplayer_session_users.UserID', $user->UserID))
            ->latest()
            ->first();

        if ($existing) {
            return response()->json($this->sessionResponse($existing, $user->UserID));
        }

        DB::beginTransaction();
        try {
            // Keresünk várakozó session-t amiben még csak 1 ember van
            $session = MultiplayerSession::where('LevelID', $levelId)
                ->where('Status', 'waiting')
                ->whereDoesntHave('users', fn($q) => $q->where('multiplayer_session_users.UserID', $user->UserID))
                ->lockForUpdate()
                ->first();

            if ($session) {
                // Csatlakozunk a meglévő sessionhöz
                $session->users()->attach($user->UserID, ['IsReady' => false]);
                $userCount = $session->users()->count();
                if ($userCount >= 2) {
                    $session->Status = 'playing';
                    $session->save();
                }
            } else {
                // Új session
                $session = MultiplayerSession::create([
                    'LevelID'        => $levelId,
                    'Status'         => 'waiting',
                    'SolvedQuestions'=> [],
                ]);
                $session->users()->attach($user->UserID, ['IsReady' => false]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hiba történt. Próbáld újra.'], 500);
        }

        return response()->json($this->sessionResponse($session->fresh(['users']), $user->UserID));
    }

    /**
     * GET /api/multiplayer/{sessionId}/state
     * Lekéri a session aktuális állapotát (polling).
     */
    public function state(Request $request, int $sessionId)
    {
        $session = MultiplayerSession::with('users')->find($sessionId);

        // Session törölve (pl. mindenki kilépett) → 404
        if (!$session) {
            return response()->json(['message' => 'Session nem található.'], 404);
        }

        $user = $request->user();

        if (!$session->users->contains('UserID', $user->UserID)) {
            return response()->json(['message' => 'Nincs jogosultságod.'], 403);
        }

        // Ha abandoned: töröljük a maradék játékos progress-ét is, majd a sessiont
        if ($session->Status === 'abandoned') {
            ProgressController::doResetLevelProgress($user->UserID, $session->LevelID);
            $session->users()->detach($user->UserID);
            if ($session->fresh()->users()->count() === 0) {
                $session->delete();
            }
        }

        return response()->json($this->sessionResponse($session, $user->UserID));
    }

    /**
     * POST /api/multiplayer/{sessionId}/solve
     * Egy kérdés megoldásának bejelentése.
     * Body: { "question_id": 42, "reward_digit": 7 }
     */
    public function solve(Request $request, int $sessionId)
    {
        $request->validate([
            'question_id'  => 'required|integer',
            'reward_digit' => 'required|integer|min:0|max:9',
        ]);
        $session    = MultiplayerSession::with('users')->findOrFail($sessionId);
        $user       = $request->user();
        $questionId = $request->input('question_id');
        $digit      = $request->input('reward_digit');

        if (!$session->users->contains('UserID', $user->UserID)) {
            return response()->json(['message' => 'Nincs jogosultságod.'], 403);
        }
        if ($session->Status !== 'playing') {
            return response()->json(['message' => 'A session nem aktív.'], 409);
        }

        // SolvedQuestions: [ { "id": 42, "digit": 7 }, ... ]
        $solved = $session->SolvedQuestions ?? [];
        $exists = collect($solved)->contains(fn($item) => (int)($item['id'] ?? $item) === $questionId);
        if (!$exists) {
            $solved[] = ['id' => $questionId, 'digit' => $digit];
            $session->SolvedQuestions = $solved;
            $session->save();
        }

        return response()->json($this->sessionResponse($session->fresh(['users']), $user->UserID));
    }

    /**
     * POST /api/multiplayer/{sessionId}/finish
     * A session lezárása (helyes kód beküldése után).
     * Minden résztvevőnek elmenti a UserProgress-t (Completed = true),
     * de leaderboard-ra NEM kerülnek fel.
     */
    public function finish(Request $request, int $sessionId)
    {
        $session = MultiplayerSession::with('users')->findOrFail($sessionId);
        $user    = $request->user();

        if (!$session->users->contains('UserID', $user->UserID)) {
            return response()->json(['message' => 'Nincs jogosultságod.'], 403);
        }

        $levelId = $session->LevelID;

        // Minden résztvevőnek mentjük a progress-t
        foreach ($session->users as $sessionUser) {
            $alreadyCompleted = UserProgress::where('UserID', $sessionUser->UserID)
                ->where('LevelID', $levelId)
                ->where('Completed', true)
                ->exists();

            if (!$alreadyCompleted) {
                UserProgress::updateOrCreate(
                    ['UserID' => $sessionUser->UserID, 'LevelID' => $levelId],
                    [
                        'Completed'   => true,
                        'TimeSpent'   => 0,
                        'CompletedAt' => now(),
                    ]
                );
            }
        }

        $session->Status = 'finished';
        $session->save();

        return response()->json(['message' => 'Session lezárva.']);
    }

    /**
     * DELETE /api/multiplayer/{sessionId}/leave
     * Kilépés a sessionből. Ha a session már nem létezik, 200-at adunk vissza.
     * Minden résztvevő adott szobához tartozó UserProgress-e törlődik.
     * Ha még maradt játékos, a session 'abandoned' státuszra vált (a partner polling észleli).
     */
    public function leave(Request $request, int $sessionId)
    {
        $session = MultiplayerSession::with('users')->find($sessionId);

        // Session már nem létezik → OK, nincs mit csinálni
        if (!$session) {
            return response()->json(['message' => 'Session nem található.'], 200);
        }

        $user    = $request->user();
        $levelId = $session->LevelID;

        // Minden résztvevő progress-ét töröljük ennél a szobánál
        foreach ($session->users as $sessionUser) {
            ProgressController::doResetLevelProgress($sessionUser->UserID, $levelId);
        }

        $session->users()->detach($user->UserID);

        $remaining = $session->fresh()->users()->count();

        if ($remaining === 0) {
            // Senki sem maradt → töröljük a sessiont
            $session->delete();
        } else {
            // Még van játékos → 'abandoned' státusz, hogy a partner polling észlelje
            $session->Status = 'abandoned';
            $session->save();
        }

        return response()->json(['message' => 'Kilépve a sessionből.']);
    }

    // ─── Privát helper ───────────────────────────────────────────────────────
    private function sessionResponse(MultiplayerSession $session, int $myUserId): array
    {
        $players = $session->users->map(fn($u) => [
            'UserID'   => $u->UserID,
            'Username' => $u->Username,
            'IsReady'  => (bool) $u->pivot->IsReady,
        ])->values();

        // SolvedQuestions normalizálása: mindig [ {id, digit} ] formátum
        $rawSolved = $session->SolvedQuestions ?? [];
        $solvedNormalized = collect($rawSolved)->map(function ($item) {
            if (is_array($item) && isset($item['id'])) {
                return ['id' => (int)$item['id'], 'digit' => (int)$item['digit']];
            }
            // régi formátum (csak int) → digit ismeretlen
            return ['id' => (int)$item, 'digit' => 0];
        })->values()->toArray();

        return [
            'id'              => $session->id,
            'LevelID'         => $session->LevelID,
            'Status'          => $session->Status,
            'SolvedQuestions' => $solvedNormalized,
            'Players'         => $players,
            'MyUserID'        => $myUserId,
        ];
    }
}
