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
                'Category'    => 'Nehéz',
                'OrderNumber' => 1,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room1/background.png',
            ],
            [
                'Name'        => 'A Laboratorium',
                'Description' => 'Egy elhagyatott tudományos laborban találod magad. Kémcsövek, lombikak és rejtélyes gépek vesznek körül. A tudós naplója talán segít!',
                'Category'    => 'Nehéz',
                'OrderNumber' => 2,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room2/background.png',
            ],
            [
                'Name'        => 'A Kastély Pincéje',
                'Description' => 'Sötét, nedves pince, ahol a falak kövei évszázadok titkait őrzik. Egy régi térkép és egy rozsdás lakat az egyetlen segítséged.',
                'Category'    => 'Nehéz',
                'OrderNumber' => 3,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room3/background.png',
            ],
            [
                'Name'        => 'A Kapitány Kabinja',
                'Description' => 'Egy elsüllyedt hajó kapitányának kabinjában ébredsz fel. A tenger mélyén kell megtalálnod a kijárathoz vezető kódot.',
                'Category'    => 'Nehéz',
                'OrderNumber' => 4,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room4/background.png',
            ],
            [
                'Name'        => 'Az Űrállomás',
                'Description' => 'Egy elhagyott űrállomáson vagy, és az oxigénkészlet fogytán. A számítógépek adatai között kell megtalálnod a mentőkapsulához szükséges kódot!',
                'Category'    => 'Nehéz',
                'OrderNumber' => 5,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room5/background.png',
            ],

            // ── Könnyed (OrderNumber 6-10) ───────────────────────────────
            [
                'Name'        => 'A Játékszoba',
                'Description' => 'Egy vidám, színes játékszobában találod magad. Rejtvények és egyszerű fejtörők várnak rád – tökéletes melegítő a komolyabb szobák előtt!',
                'Category'    => 'Könnyed',
                'OrderNumber' => 6,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room6/background.png',
            ],
            [
                'Name'        => 'A Kávézó',
                'Description' => 'Egy kellemes kis kávézóban ragadtál be. A pultár elrejtette a kijárati kódot – de a nyomok könnyen megtalálhatók, ha jól nézel körül!',
                'Category'    => 'Könnyed',
                'OrderNumber' => 7,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room7/background.png',
            ],
            [
                'Name'        => 'Az Osztályterem',
                'Description' => 'Bezártak az iskolai osztályterembe! Szerencsére az összes nyom ott van a táblán és a padokon. Alapiskolás tudással megoldható minden kérdés.',
                'Category'    => 'Könnyed',
                'OrderNumber' => 8,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room8/background.png',
            ],
            [
                'Name'        => 'A Kert',
                'Description' => 'Egy gyönyörű kertben vándorolsz, ahol a virágok és fák között rejtőzik a kód. A természet szerelmeseinek könnyen megy majd!',
                'Category'    => 'Könnyed',
                'OrderNumber' => 9,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room9/background.png',
            ],
            [
                'Name'        => 'A Cukrászda',
                'Description' => 'Édes illatok, torták és aprósütemények vesznek körül. A cukrász elrejtette a receptjét – és benne a kódot. Édes fejtörők várnak!',
                'Category'    => 'Könnyed',
                'OrderNumber' => 10,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room10/background.png',
            ],

            // ── Közepes (OrderNumber 11-15) ──────────────────────────────
            [
                'Name'        => 'A Detektív Irodája',
                'Description' => 'Egy magánnyomozó irodájában ébredsz. Aktákat, fotókat és titkos üzeneteket kell megfejtened, hogy megtaláld a széfhez vezető kódot.',
                'Category'    => 'Közepes',
                'OrderNumber' => 11,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room11/background.png',
            ],
            [
                'Name'        => 'A Múzeum',
                'Description' => 'Egy ókori műtárgyakkal teli múzeumban ragadtál be. A kurátori jegyzetek és a kiállítási táblák között rejtőznek a nyomok.',
                'Category'    => 'Közepes',
                'OrderNumber' => 12,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room12/background.png',
            ],
            [
                'Name'        => 'A Téli Kunyhó',
                'Description' => 'Egy havas hegycsúcson lévő menedékkunyhóban vagy beragadva. A vihar elvonultáig meg kell találnod a rádiókód kombinációját.',
                'Category'    => 'Közepes',
                'OrderNumber' => 13,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room13/background.png',
            ],
            [
                'Name'        => 'A Hajógyár',
                'Description' => 'Egy régi hajógyárban jársz, ahol rozsdás gépek és tervrajzok veszik körül. A mérnöki dokumentációban rejtőzik a biztonsági kód.',
                'Category'    => 'Közepes',
                'OrderNumber' => 14,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room14/background.png',
            ],
            [
                'Name'        => 'A Varázslatos Könyvtár',
                'Description' => 'Egy titkos varázslatos könyvtárba kerültél, ahol a könyvek maguktól mozognak. A varázslat feloldásához meg kell találnod az ősi kódot.',
                'Category'    => 'Közepes',
                'OrderNumber' => 15,
                'IsActive'    => true,
                'BackgroundUrl' => '/rooms/room15/background.png',
            ],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }
    }
}
