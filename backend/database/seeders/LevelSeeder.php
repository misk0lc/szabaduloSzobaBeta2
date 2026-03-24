<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            [
                'Name'        => 'A Könyvtárszoba',
                'Description' => 'Egy poros, régi könyvtárban ragadtál. A falakat könyvek borítják, valahol el van rejtve a kód. Találd meg a könyvek között elrejtett nyomokat!',
                'OrderNumber' => 1,
                'IsActive'    => true,
            ],
            [
                'Name'        => 'A Laboratorium',
                'Description' => 'Egy elhagyatott tudományos laborban találod magad. Kémcsövek, lombikak és rejtélyes gépek vesznek körül. A tudós naplója talán segít!',
                'OrderNumber' => 2,
                'IsActive'    => true,
            ],
            [
                'Name'        => 'A Kastély Pincéje',
                'Description' => 'Sötét, nedves pince, ahol a falak kövei évszázadok titkait őrzik. Egy régi térkép és egy rozsdás lakat az egyetlen segítséged.',
                'OrderNumber' => 3,
                'IsActive'    => true,
            ],
            [
                'Name'        => 'A Kapitány Kabinja',
                'Description' => 'Egy elsüllyedt hajó kapitányának kabinjában ébredsz fel. A tenger mélyén kell megtalálnod a kijárathoz vezető kódot.',
                'OrderNumber' => 4,
                'IsActive'    => true,
            ],
            [
                'Name'        => 'Az Űrállomás',
                'Description' => 'Egy elhagyott űrállomáson vagy, és az oxigénkészlet fogytán. A számítógépek adatai között kell megtalálnod a mentőkapsulához szükséges kódot!',
                'OrderNumber' => 5,
                'IsActive'    => true,
            ],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
