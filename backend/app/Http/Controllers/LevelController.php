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

        $result = [];

        foreach ($levels as $index => $level) {
            $isCompleted = in_array($level->LevelID, $completedLevelIds);

            // Unlock logika:
            // - Az első pálya mindig unlock-olt
            // - Minden következő pálya csak akkor unlock-olt, ha az előző completed
            if ($index === 0) {
                $isUnlocked = true;
            } else {
                $previousLevel = $levels[$index - 1];
                $isUnlocked    = in_array($previousLevel->LevelID, $completedLevelIds);
            }

            // Csak az első nem-completed, unlock-olt pálya az "aktív"
            $isActive = $isUnlocked && !$isCompleted;

            $result[] = [
                'LevelID'      => $level->LevelID,
                'Name'         => $level->Name,
                'Description'  => $level->Description,
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
            'OrderNumber'  => $level->OrderNumber,
            'BackgroundUrl'=> $level->BackgroundUrl,
            'IsCompleted'  => $progress?->Completed ?? false,
            'TimeSpent'    => $progress?->TimeSpent ?? 0,
            'CompletedAt'  => $progress?->CompletedAt,
        ]);
    }
}
