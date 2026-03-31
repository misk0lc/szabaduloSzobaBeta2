<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelBackgroundSeeder extends Seeder
{
    public function run(): void
    {
        $backgrounds = [
            1  => '/rooms/room1/background.png',
            2  => '/rooms/room2/background.png',
            3  => '/rooms/room3/background.png',
            4  => '/rooms/room4/background.png',
            5  => '/rooms/room5/background.png',
            6  => '/rooms/room6/background.png',
            7  => '/rooms/room7/background.png',
            8  => '/rooms/room8/background.png',
            9  => '/rooms/room9/background.png',
            10 => '/rooms/room10/background.png',
            11 => '/rooms/room11/background.png',
            12 => '/rooms/room12/background.png',
            13 => '/rooms/room13/background.png',
            14 => '/rooms/room14/background.png',
            15 => '/rooms/room15/background.png',
        ];

        foreach ($backgrounds as $orderNumber => $path) {
            Level::where('OrderNumber', $orderNumber)
                ->update(['BackgroundUrl' => $path]);
        }
    }
}
