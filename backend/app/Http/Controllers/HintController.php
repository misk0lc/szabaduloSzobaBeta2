<?php

namespace App\Http\Controllers;

use App\Models\LeaderboardEntry;
use App\Models\UserMoney;
use Illuminate\Http\Request;

class HintController extends Controller
{
    /**
     * POST /api/hints/use5050
     * 50/50 tipp: 25 pénz levonás + HintsUsed növelés a ranglistán.
     */
    public function use5050(Request $request)
    {
        $user = $request->user();
        $cost = 25;

        $money = UserMoney::firstOrCreate(
            ['UserID' => $user->UserID],
            ['Amount' => 0]
        );

        if ($money->Amount < $cost) {
            return response()->json([
                'message' => 'Nincs elegendő pénzed a 50/50 használatához.',
                'Balance' => $money->Amount,
            ], 422);
        }

        $money->decrement('Amount', $cost);

        $leaderboard = LeaderboardEntry::firstOrCreate(
            ['UserID' => $user->UserID],
            ['Score' => 0, 'LevelsCompleted' => 0, 'TimeTotal' => 0, 'HintsUsed' => 0]
        );
        $leaderboard->increment('HintsUsed');

        return response()->json([
            'NewBalance' => $money->fresh()->Amount,
            'HintsUsed'  => $leaderboard->fresh()->HintsUsed,
        ]);
    }
}
