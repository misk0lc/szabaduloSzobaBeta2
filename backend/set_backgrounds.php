<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Level;

// Képek a pálya nevéhez kötve (name tartalmaz kulcsszót)
$map = [
    'könyvtár' => '/rooms/room1/background.png',
    'laborat'  => '/rooms/room2/background.png',
    'pince'    => '/rooms/room3/background.png',
    'kapitány' => '/rooms/room4/background.png',
    'űr'       => '/rooms/room5/background.png',
];

$levels = Level::all();
foreach ($levels as $level) {
    $name = mb_strtolower($level->Name);
    foreach ($map as $keyword => $url) {
        if (str_contains($name, $keyword)) {
            $level->BackgroundUrl = $url;
            $level->save();
            echo "Updated: [{$level->LevelID}] {$level->Name} -> {$url}\n";
            break;
        }
    }
}
echo "Done.\n";
