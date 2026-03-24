<?php

namespace App\Http\Controllers;

use App\Http\Traits\ChecksLevelUnlock;
use App\Models\Level;
use App\Models\LeaderboardEntry;
use App\Models\Question;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    use ChecksLevelUnlock;

    /**
     * POST /api/levels/{levelId}/submit-code
     *
     * Body: { "code": "1234", "timeSpent": 120 }
     *
     * Backend ellenőrzi a beküldött kódot a pálya összes RewardDigit-je alapján
     * (Questions.OrderNumber / PositionX sorrendben).
     * Ha helyes → Level completed, következő pálya unlock, score számítás.
     */
    public function submitCode(Request $request, int $levelId)
    {
        $request->validate([
            'code'      => 'required|string',
            'timeSpent' => 'required|integer|min:0',
        ]);

        $user  = $request->user();
        $level = Level::where('LevelID', $levelId)->where('IsActive', true)->firstOrFail();

        // Unlock ellenőrzés
        if (!$this->isLevelUnlocked($user->UserID, $levelId)) {
            return response()->json(['message' => 'Ez a pálya még nem elérhető.'], 403);
        }

        // Már teljesítve van?
        $progress = UserProgress::where('UserID', $user->UserID)
            ->where('LevelID', $levelId)
            ->first();

        if ($progress && $progress->Completed) {
            return response()->json(['message' => 'Ezt a pályát már teljesítetted.'], 409);
        }

        // A helyes kód összeállítása: kérdések RewardDigit-jei PositionX sorrendben
        $questions = Question::where('LevelID', $levelId)
            ->orderBy('PositionX')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'A pályához nem tartoznak kérdések.'], 422);
        }

        $correctCode = $questions->pluck('RewardDigit')->join('');
        $givenCode   = trim($request->input('code'));
        $timeSpent   = (int) $request->input('timeSpent');

        if ($givenCode !== $correctCode) {
            return response()->json([
                'correct' => false,
                'message' => 'Hibás kód. Próbáld újra!',
            ], 200);
        }

        // ─── Helyes kód ──────────────────────────────────────────────────────

        // Progress mentése / frissítése
        $progress = UserProgress::updateOrCreate(
            ['UserID' => $user->UserID, 'LevelID' => $levelId],
            [
                'Completed'   => true,
                'TimeSpent'   => $timeSpent,
                'CompletedAt' => now(),
            ]
        );

        // Score számítás:
        // - alap pont: 1000
        // - időlevonás: minden másodperc után -1 pont (min. 100)
        // - hint bónusz: nincs levonás a hintek miatt (azt már megfizette)
        $score = max(100, 1000 - $timeSpent);

        // Leaderboard frissítése
        $leaderboard = LeaderboardEntry::firstOrCreate(
            ['UserID' => $user->UserID],
            [
                'Score'           => 0,
                'LevelsCompleted' => 0,
                'TimeTotal'       => 0,
                'HintsUsed'       => 0,
            ]
        );

        $leaderboard->increment('Score', $score);
        $leaderboard->increment('LevelsCompleted');
        $leaderboard->increment('TimeTotal', $timeSpent);

        // Következő pálya meghatározása
        $nextLevel = Level::where('OrderNumber', $level->OrderNumber + 1)
            ->where('IsActive', true)
            ->first();

        return response()->json([
            'correct'         => true,
            'message'         => 'Gratulálok! Pálya teljesítve!',
            'Score'           => $score,
            'TimeSpent'       => $timeSpent,
            'CompletedAt'     => $progress->CompletedAt,
            'TotalScore'      => $leaderboard->fresh()->Score,
            'LevelsCompleted' => $leaderboard->fresh()->LevelsCompleted,
            'NextLevel'       => $nextLevel ? [
                'LevelID'     => $nextLevel->LevelID,
                'Name'        => $nextLevel->Name,
                'OrderNumber' => $nextLevel->OrderNumber,
            ] : null,
        ]);
    }
}
