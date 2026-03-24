<?php

namespace App\Http\Controllers;

use App\Models\Hint;
use App\Models\Level;
use App\Models\Question;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserMoney;
use App\Models\UserProgress;
use App\Models\LeaderboardEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── USERS ──────────────────────────────────────────────────

    public function users(Request $request)
    {
        $q = $request->query('q', '');
        $query = User::query();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('Username', 'like', "%{$q}%")
                  ->orWhere('Email', 'like', "%{$q}%");
            });
        }

        $users = $query->orderByDesc('UserID')->get()->map(fn($u) => [
            'UserID'   => $u->UserID,
            'Username' => $u->Username,
            'Email'    => $u->Email,
            'IsAdmin'  => $u->IsAdmin,
            'IsActive' => $u->IsActive,
            'CreatedAt'=> $u->CreatedAt,
            'Balance'  => $u->money?->Amount ?? 0,
            'Score'    => $u->leaderboard?->Score ?? 0,
        ]);

        return response()->json($users);
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'Username' => 'sometimes|string|max:50',
            'Email'    => 'sometimes|email|max:100',
            'IsAdmin'  => 'sometimes|boolean',
            'IsActive' => 'sometimes|boolean',
            'Password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['Password'])) {
            $user->PasswordHash = Hash::make($validated['Password']);
            unset($validated['Password']);
        }

        $user->fill($validated);
        $user->save();

        return response()->json(['message' => 'Felhasználó frissítve.', 'user' => [
            'UserID'   => $user->UserID,
            'Username' => $user->Username,
            'Email'    => $user->Email,
            'IsAdmin'  => $user->IsAdmin,
            'IsActive' => $user->IsActive,
        ]]);
    }

    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Felhasználó törölve.']);
    }

    // ─── LEVELS ─────────────────────────────────────────────────

    public function levels()
    {
        $levels = Level::orderBy('OrderNumber')->get();
        return response()->json($levels);
    }

    public function createLevel(Request $request)
    {
        $validated = $request->validate([
            'Name'          => 'required|string|max:100',
            'Description'   => 'required|string|max:2000',
            'OrderNumber'   => 'required|integer|min:1',
            'IsActive'      => 'sometimes|boolean',
            'BackgroundUrl' => 'nullable|string|max:500',
        ]);

        $level = Level::create($validated);
        return response()->json($level, 201);
    }

    public function updateLevel(Request $request, int $id)
    {
        $level = Level::findOrFail($id);

        $validated = $request->validate([
            'Name'          => 'sometimes|string|max:100',
            'Description'   => 'sometimes|string|max:2000',
            'OrderNumber'   => 'sometimes|integer|min:1',
            'IsActive'      => 'sometimes|boolean',
            'BackgroundUrl' => 'nullable|string|max:500',
        ]);

        $level->update($validated);
        return response()->json($level);
    }

    public function deleteLevel(int $id)
    {
        Level::findOrFail($id)->delete();
        return response()->json(['message' => 'Pálya törölve.']);
    }

    // ─── QUESTIONS ──────────────────────────────────────────────

    public function questions(Request $request)
    {
        $levelId = $request->query('level_id');
        $query = Question::with('level:LevelID,Name');

        if ($levelId) {
            $query->where('LevelID', $levelId);
        }

        $questions = $query->orderBy('LevelID')->orderBy('PositionX')->get();
        return response()->json($questions);
    }

    public function createQuestion(Request $request)
    {
        $validated = $request->validate([
            'LevelID'       => 'required|integer|exists:levels,LevelID',
            'QuestionText'  => 'required|string|max:1000',
            'CorrectAnswer' => 'required|string|max:255',
            'RewardDigit'   => 'required|integer|min:0|max:9',
            'MoneyReward'   => 'required|integer|min:0',
            'PositionX'     => 'required|integer|min:1|max:20',
            'PositionY'     => 'required|integer|min:1|max:4',
        ]);

        $question = Question::create($validated);
        return response()->json($question, 201);
    }

    public function updateQuestion(Request $request, int $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'QuestionText'  => 'sometimes|string|max:1000',
            'CorrectAnswer' => 'sometimes|string|max:255',
            'RewardDigit'   => 'sometimes|integer|min:0|max:9',
            'MoneyReward'   => 'sometimes|integer|min:0',
            'PositionX'     => 'sometimes|integer|min:1|max:20',
            'PositionY'     => 'sometimes|integer|min:1|max:4',
        ]);

        $question->update($validated);
        return response()->json($question);
    }

    public function deleteQuestion(int $id)
    {
        Question::findOrFail($id)->delete();
        return response()->json(['message' => 'Kérdés törölve.']);
    }

    // ─── HINTS ──────────────────────────────────────────────────

    public function hints(Request $request)
    {
        $questionId = $request->query('question_id');
        $query = Hint::with('question:QuestionID,QuestionText,LevelID');

        if ($questionId) {
            $query->where('QuestionID', $questionId);
        }

        $hints = $query->orderBy('QuestionID')->orderBy('HintOrder')->get();
        return response()->json($hints);
    }

    public function createHint(Request $request)
    {
        $validated = $request->validate([
            'QuestionID' => 'required|integer|exists:questions,QuestionID',
            'HintText'   => 'required|string|max:500',
            'Cost'       => 'required|integer|min:0',
            'HintOrder'  => 'required|integer|min:1',
        ]);

        $hint = Hint::create($validated);
        return response()->json($hint, 201);
    }

    public function updateHint(Request $request, int $id)
    {
        $hint = Hint::findOrFail($id);

        $validated = $request->validate([
            'HintText'   => 'sometimes|string|max:500',
            'Cost'       => 'sometimes|integer|min:0',
            'HintOrder'  => 'sometimes|integer|min:1',
        ]);

        $hint->update($validated);
        return response()->json($hint);
    }

    public function deleteHint(int $id)
    {
        Hint::findOrFail($id)->delete();
        return response()->json(['message' => 'Tipp törölve.']);
    }

    // ─── STATS ──────────────────────────────────────────────────

    public function stats()
    {
        return response()->json([
            'totalUsers'      => User::count(),
            'activeUsers'     => User::where('IsActive', true)->count(),
            'totalLevels'     => Level::count(),
            'totalQuestions'  => Question::count(),
            'totalHints'      => Hint::count(),
            'totalAnswers'    => UserAnswer::count(),
            'correctAnswers'  => UserAnswer::where('IsCorrect', true)->count(),
            'completedRooms'  => UserProgress::where('Completed', true)->count(),
        ]);
    }
}
