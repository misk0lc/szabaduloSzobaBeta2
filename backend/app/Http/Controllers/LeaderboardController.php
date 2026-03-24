<?php

namespace App\Http\Controllers;

use App\Models\LeaderboardEntry;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * GET /api/leaderboard
     * Top 10 játékos, score szerinti csökkenő sorrendben.
     */
    public function index()
    {
        $entries = LeaderboardEntry::with('user:UserID,Username')
            ->orderByDesc('Score')
            ->limit(10)
            ->get()
            ->map(fn($e) => [
                'UserID'          => $e->UserID,
                'Username'        => $e->user?->Username ?? 'Ismeretlen',
                'Score'           => $e->Score,
                'LevelsCompleted' => $e->LevelsCompleted,
                'TimeTotal'       => $e->TimeTotal,
                'HintsUsed'       => $e->HintsUsed,
            ]);

        return response()->json($entries);
    }
}
