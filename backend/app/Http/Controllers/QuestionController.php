<?php

namespace App\Http\Controllers;

use App\Http\Traits\ChecksLevelUnlock;
use App\Models\Question;
use App\Models\UserAnswer;
use App\Models\UserMoney;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use ChecksLevelUnlock;
    /**
     * GET /api/levels/{levelId}/questions
     * Visszaadja a pálya kérdéseit (CorrectAnswer nélkül!).
     */
    public function index(Request $request, int $levelId)
    {
        $user = $request->user();

        // Unlock ellenőrzés
        if (!$this->isLevelUnlocked($user->UserID, $levelId)) {
            return response()->json(['message' => 'Ez a pálya még nem elérhető.'], 403);
        }

        // Lekérjük a user helyes válaszait ehhez a pályához
        $correctQuestionIds = UserAnswer::where('UserID', $user->UserID)
            ->where('IsCorrect', true)
            ->pluck('QuestionID')
            ->toArray();

        $allQuestions = Question::where('LevelID', $levelId)
            ->with('options')
            ->orderBy('PositionX')
            ->get();

        $questions = $allQuestions->map(function ($q) use ($correctQuestionIds) {
            $solved = in_array($q->QuestionID, $correctQuestionIds);

            // Opciók keverve, IsCorrect mező nélkül (nem árulhatjuk el a helyes választ)
            $options = $q->options->shuffle()->map(fn($o) => [
                'OptionID'   => $o->OptionID,
                'OptionText' => $o->OptionText,
                'IsCorrect'  => $o->IsCorrect,
            ])->values();

            return [
                'QuestionID'   => $q->QuestionID,
                'QuestionText' => $q->QuestionText,
                'PositionX'    => $q->PositionX,
                'PositionY'    => $q->PositionY,
                'MoneyReward'  => $q->MoneyReward,
                'Solved'       => $solved,
                'RewardDigit'  => $solved ? $q->RewardDigit : null,
                'Options'      => $options,
            ];
        });

        return response()->json($questions);
    }

    /**
     * POST /api/questions/{id}/check-answer
     * Ellenőrzi a felhasználó válaszát.
     *
     * Body: { "answer": "..." }
     */
    public function checkAnswer(Request $request, int $id)
    {
        $request->validate([
            'answer' => 'required|string|max:255',
        ]);

        $user     = $request->user();
        $question = Question::findOrFail($id);

        // Unlock ellenőrzés
        if (!$this->isLevelUnlocked($user->UserID, $question->LevelID)) {
            return response()->json(['message' => 'Ez a pálya még nem elérhető.'], 403);
        }

        $givenAnswer   = trim($request->input('answer'));
        $correctAnswer = trim($question->CorrectAnswer);
        $isCorrect     = strcasecmp($givenAnswer, $correctAnswer) === 0;

        // Válasz naplózása
        UserAnswer::create([
            'UserID'      => $user->UserID,
            'QuestionID'  => $question->QuestionID,
            'GivenAnswer' => $givenAnswer,
            'IsCorrect'   => $isCorrect,
        ]);

        // Pénztárca lekérése vagy létrehozása
        $money = UserMoney::firstOrCreate(
            ['UserID' => $user->UserID],
            ['Amount' => 0]
        );

        if ($isCorrect) {
            // Helyes válasz: pénzjutalom hozzáadása
            $money->increment('Amount', $question->MoneyReward);

            return response()->json([
                'correct'     => true,
                'message'     => 'Helyes válasz!',
                'RewardDigit' => $question->RewardDigit,
                'MoneyReward' => $question->MoneyReward,
                'NewBalance'  => $money->fresh()->Amount,
            ]);
        } else {
            // Hibás válasz: opcionális levonás (10 egység, de min. 0)
            $penalty = 10;
            $newAmount = max(0, $money->Amount - $penalty);
            $money->update(['Amount' => $newAmount]);

            return response()->json([
                'correct'    => false,
                'message'    => 'Hibás válasz. Próbáld újra!',
                'Penalty'    => $penalty,
                'NewBalance' => $newAmount,
            ], 200);
        }
    }
}
