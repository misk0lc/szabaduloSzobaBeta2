<?php

namespace App\Http\Controllers;

use App\Models\Hint;
use App\Models\LeaderboardEntry;
use App\Models\UserMoney;
use Illuminate\Http\Request;

class HintController extends Controller
{
    /**
     * GET /api/questions/{questionId}/hints
     * Visszaadja a kérdés elérhető tippjeit (HintText nélkül!).
     */
    public function index(int $questionId)
    {
        $hints = Hint::where('QuestionID', $questionId)
            ->orderBy('HintOrder')
            ->get()
            ->map(fn($h) => [
                'HintID'    => $h->HintID,
                'HintOrder' => $h->HintOrder,
                'Cost'      => $h->Cost,
                // HintText NEM kerül ki vásárlás előtt!
            ]);

        return response()->json($hints);
    }

    /**
     * POST /api/hints/{id}/buy
     * Megvásárolja a tippet, levonja a pénzt, visszaküldi a szöveget.
     */
    public function buy(Request $request, int $id)
    {
        $user = $request->user();
        $hint = Hint::findOrFail($id);

        // Pénztárca lekérése vagy létrehozása
        $money = UserMoney::firstOrCreate(
            ['UserID' => $user->UserID],
            ['Amount' => 0]
        );

        // Elegendő pénz ellenőrzése
        if ($money->Amount < $hint->Cost) {
            return response()->json([
                'message'    => 'Nincs elegendő pénzed ehhez a tipphez.',
                'Balance'    => $money->Amount,
                'HintCost'   => $hint->Cost,
                'Missing'    => $hint->Cost - $money->Amount,
            ], 422);
        }

        // Pénz levonása
        $money->decrement('Amount', $hint->Cost);

        // Leaderboard hint count növelése
        $leaderboard = LeaderboardEntry::firstOrCreate(
            ['UserID' => $user->UserID],
            [
                'Score'           => 0,
                'LevelsCompleted' => 0,
                'TimeTotal'       => 0,
                'HintsUsed'       => 0,
            ]
        );
        $leaderboard->increment('HintsUsed');

        return response()->json([
            'message'    => 'Tipp sikeresen megvásárolva.',
            'HintID'     => $hint->HintID,
            'HintOrder'  => $hint->HintOrder,
            'HintText'   => $hint->HintText,
            'Cost'       => $hint->Cost,
            'NewBalance' => $money->fresh()->Amount,
            'HintsUsed'  => $leaderboard->fresh()->HintsUsed,
        ]);
    }
}
