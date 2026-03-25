<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProgressController;
use App\Models\Level;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserMoney;
use App\Models\UserProgress;
use App\Models\LeaderboardEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
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
        return response()->json(['message' => 'Felhasznalo frissitve.', 'user' => [
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
        return response()->json(['message' => 'Felhasznalo torolve.']);
    }

    public function levels()
    {
        return response()->json(Level::orderBy('OrderNumber')->get());
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
        return response()->json(Level::create($validated), 201);
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
        return response()->json(['message' => 'Palya torolve.']);
    }

    public function questions(Request $request)
    {
        $levelId = $request->query('level_id');
        $query = Question::with(['level:LevelID,Name', 'options']);
        if ($levelId) {
            $query->where('LevelID', $levelId);
        }
        return response()->json($query->orderBy('LevelID')->orderBy('PositionX')->get());
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
            'options'       => 'sometimes|array|size:4',
            'options.*.OptionText' => 'required_with:options|string|max:255',
            'options.*.IsCorrect'  => 'required_with:options|boolean',
        ]);
        $question = Question::create(\Arr::except($validated, ['options']));
        if (!empty($validated['options'])) {
            foreach ($validated['options'] as $opt) {
                $question->options()->create(['OptionText' => $opt['OptionText'], 'IsCorrect' => $opt['IsCorrect']]);
            }
        }
        return response()->json($question->load('options'), 201);
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
            'options'       => 'sometimes|array|size:4',
            'options.*.OptionText' => 'required_with:options|string|max:255',
            'options.*.IsCorrect'  => 'required_with:options|boolean',
        ]);
        $question->update(\Arr::except($validated, ['options']));
        if (isset($validated['options'])) {
            $question->options()->delete();
            foreach ($validated['options'] as $opt) {
                $question->options()->create(['OptionText' => $opt['OptionText'], 'IsCorrect' => $opt['IsCorrect']]);
            }
        }
        return response()->json($question->load('options'));
    }

    public function deleteQuestion(int $id)
    {
        Question::findOrFail($id)->delete();
        return response()->json(['message' => 'Kerdes torolve.']);
    }

    public function reports(Request $request)
    {
        $status = $request->query('status');
        $query  = Report::with('user:UserID,Username,Email')->latest();
        if ($status) {
            $query->where('Status', $status);
        }
        return response()->json($query->get());
    }

    public function updateReport(Request $request, int $id)
    {
        $report = Report::findOrFail($id);
        $validated = $request->validate(['Status' => 'required|in:new,seen,resolved']);
        $report->update($validated);
        return response()->json($report);
    }

    public function deleteReport(int $id)
    {
        Report::findOrFail($id)->delete();
        return response()->json(['message' => 'Report torolve.']);
    }

    /**
     * DELETE /api/admin/users/{id}/reset-progress
     * Admin bármikor resetelheti bármelyik user haladását.
     */
    public function resetUserProgress(int $id)
    {
        User::findOrFail($id); // 404 ha nincs
        ProgressController::doResetProgress($id);
        return response()->json(['message' => 'Felhasználó haladása törölve.']);
    }

    public function stats()
    {
        return response()->json([
            'totalUsers'     => User::count(),
            'activeUsers'    => User::where('IsActive', true)->count(),
            'totalLevels'    => Level::count(),
            'totalQuestions' => Question::count(),
            'totalAnswers'   => UserAnswer::count(),
            'correctAnswers' => UserAnswer::where('IsCorrect', true)->count(),
            'completedRooms' => UserProgress::where('Completed', true)->count(),
            'newReports'     => Report::where('Status', 'new')->count(),
        ]);
    }
}