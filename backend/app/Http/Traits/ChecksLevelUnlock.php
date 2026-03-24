<?php

namespace App\Http\Traits;

use App\Models\Level;
use App\Models\UserProgress;

trait ChecksLevelUnlock
{
    /**
     * Meghatározza, hogy egy levelId unlock-olt-e az adott usernek.
     * Az 1. pálya (OrderNumber = 1) mindig elérhető.
     * Minden további pálya csak akkor, ha az előző completed.
     */
    protected function isLevelUnlocked(int $userId, int $levelId): bool
    {
        $level = Level::where('LevelID', $levelId)->where('IsActive', true)->first();

        if (!$level) {
            return false;
        }

        if ($level->OrderNumber === 1) {
            return true;
        }

        $previousLevel = Level::where('OrderNumber', $level->OrderNumber - 1)
            ->where('IsActive', true)
            ->first();

        if (!$previousLevel) {
            return false;
        }

        return UserProgress::where('UserID', $userId)
            ->where('LevelID', $previousLevel->LevelID)
            ->where('Completed', true)
            ->exists();
    }
}
