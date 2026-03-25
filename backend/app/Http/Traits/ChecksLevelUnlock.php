<?php

namespace App\Http\Traits;

use App\Models\Level;
use App\Models\UserProgress;

trait ChecksLevelUnlock
{
    /**
     * Meghatározza, hogy egy levelId unlock-olt-e az adott usernek.
     * Kategóriánként az első szoba (legkisebb OrderNumber) mindig elérhető.
     * Minden további szoba csak akkor, ha az előző kategórián belüli szoba completed.
     */
    protected function isLevelUnlocked(int $userId, int $levelId): bool
    {
        $level = Level::where('LevelID', $levelId)->where('IsActive', true)->first();

        if (!$level) {
            return false;
        }

        // Az adott kategória összes szobája OrderNumber szerint rendezve
        $categoryLevels = Level::where('IsActive', true)
            ->where('Category', $level->Category)
            ->orderBy('OrderNumber')
            ->get();

        // Ha ez a kategória első szobája, mindig nyitott
        if ($categoryLevels->first()->LevelID === $levelId) {
            return true;
        }

        // Megkeressük az előző szobát a kategórián belül
        $ids = $categoryLevels->pluck('LevelID')->toArray();
        $pos = array_search($levelId, $ids);

        if ($pos === false || $pos === 0) {
            return false;
        }

        $previousLevelId = $ids[$pos - 1];

        return UserProgress::where('UserID', $userId)
            ->where('LevelID', $previousLevelId)
            ->where('Completed', true)
            ->exists();
    }
}
