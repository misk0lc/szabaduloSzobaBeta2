<?php

namespace App\Http\Controllers;

use App\Http\Traits\ChecksLevelUnlock;
use App\Models\Level;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class LevelController extends Controller
{
    use ChecksLevelUnlock;
    /**
     * GET /api/levels
     * Visszaadja az összes pályát, user-specifikus unlock státusszal.
     * Az unlock logika kizárólag backend oldalon dől el!
     */
    public function index(Request $request)
    {
        $user   = $request->user();
        $levels = Level::where('IsActive', true)->orderBy('OrderNumber')->get();

        // Lekérjük a user összes completed progress-ét
        $completedLevelIds = UserProgress::where('UserID', $user->UserID)
            ->where('Completed', true)
            ->pluck('LevelID')
            ->toArray();

        // Kategóriánként csoportosítjuk a szobákat OrderNumber szerint rendezve
        // hogy megtaláljuk az első szobát és az előző szobát kategórián belül
        $byCategory = [];
        foreach ($levels as $level) {
            $byCategory[$level->Category][] = $level;
        }

        $result = [];

        foreach ($levels as $level) {
            $isCompleted = in_array($level->LevelID, $completedLevelIds);

            // Unlock logika kategóriánként:
            // - Az adott kategória első szobája (legkisebb OrderNumber) mindig nyitott
            // - Minden további szoba a kategórián belül csak akkor nyitott,
            //   ha az előző (kategórián belüli) szoba teljesítve van
            $categoryLevels = $byCategory[$level->Category]; // már rendezett OrderNumber szerint
            $indexInCategory = array_search($level->LevelID, array_column($categoryLevels, 'LevelID'));

            if ($indexInCategory === 0) {
                $isUnlocked = true;
            } else {
                $prevLevelInCategory = $categoryLevels[$indexInCategory - 1];
                $isUnlocked = in_array($prevLevelInCategory->LevelID, $completedLevelIds);
            }

            $isActive = $isUnlocked && !$isCompleted;

            $result[] = [
                'LevelID'      => $level->LevelID,
                'Name'         => $level->Name,
                'Description'  => $level->Description,
                'Category'     => $level->Category,
                'OrderNumber'  => $level->OrderNumber,
                'BackgroundUrl'=> $level->BackgroundUrl,
                'IsUnlocked'   => $isUnlocked,
                'IsCompleted'  => $isCompleted,
                'IsActive'     => $isActive,
            ];
        }

        return response()->json($result);
    }

    /**
     * GET /api/levels/{id}
     * Egy adott pálya részletei, csak ha unlock-olt a user számára.
     */
    public function show(Request $request, int $id)
    {
        $user  = $request->user();
        $level = Level::where('LevelID', $id)->where('IsActive', true)->firstOrFail();

        // Unlock jogosultság ellenőrzése (trait)
        if (!$this->isLevelUnlocked($user->UserID, $id)) {
            return response()->json(['message' => 'Ez a pálya még nem elérhető.'], 403);
        }

        $progress = UserProgress::where('UserID', $user->UserID)
            ->where('LevelID', $id)
            ->first();

        return response()->json([
            'LevelID'      => $level->LevelID,
            'Name'         => $level->Name,
            'Description'  => $level->Description,
            'Category'     => $level->Category,
            'OrderNumber'  => $level->OrderNumber,
            'BackgroundUrl'=> $level->BackgroundUrl,
            'IsCompleted'  => $progress?->Completed ?? false,
            'TimeSpent'    => $progress?->TimeSpent ?? 0,
            'CompletedAt'  => $progress?->CompletedAt,
        ]);
    }
}
