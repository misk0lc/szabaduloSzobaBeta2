<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('Email', 'admin@szabadulo.hu')->first();
        $jatekos1 = User::where('Email', 'jatekos1@szabadulo.hu')->first();
        $jatekos2 = User::where('Email', 'jatekos2@szabadulo.hu')->first();

        $reports = [
            // --- new ---
            [
                'UserID'       => $jatekos1?->UserID,
                'Title'        => 'Nem tölt be a 2. szoba',
                'Category'     => 'bug',
                'ContactEmail' => 'jatekos1@szabadulo.hu',
                'Message'      => 'A 2. szobába belépve fekete képernyő jelenik meg, semmi más nem töltődik be. Chromeon próbáltam.',
                'Page'         => '/room/2',
                'Status'       => 'new',
            ],
            [
                'UserID'       => null,
                'Title'        => 'Elfelejtett jelszó',
                'Category'     => 'forgotten-password',
                'ContactEmail' => 'ismeretlen@gmail.com',
                'Message'      => 'Elfelejtettem a jelszavam, kérem segítsenek visszaállítani. Felhasználónevem: valakiuser.',
                'Page'         => '/login',
                'Status'       => 'new',
            ],
            [
                'UserID'       => $jatekos2?->UserID,
                'Title'        => 'Helytelen válasz elfogadva',
                'Category'     => 'bug',
                'ContactEmail' => null,
                'Message'      => 'Az 1. szoba 3. kérdésénél rossz választ adtam be, mégis helyesnek jelölte.',
                'Page'         => '/room/1',
                'Status'       => 'new',
            ],
            // --- seen ---
            [
                'UserID'       => $jatekos1?->UserID,
                'Title'        => 'Ranglista nem frissül',
                'Category'     => 'bug',
                'ContactEmail' => null,
                'Message'      => 'Teljesítettem a 3. szobát de a ranglistán még mindig 0 pont látszik mellettünk.',
                'Page'         => '/leaderboard',
                'Status'       => 'seen',
            ],
            [
                'UserID'       => null,
                'Title'        => 'Mikor lesz új szoba?',
                'Category'     => 'question',
                'ContactEmail' => 'erdeklodo@email.hu',
                'Message'      => 'Nagyon tetszik a játék! Terveznek-e új szobákat a közeljövőben?',
                'Page'         => '/game',
                'Status'       => 'seen',
            ],
            // --- resolved ---
            [
                'UserID'       => $jatekos2?->UserID,
                'Title'        => 'Bejelentkezés nem működik',
                'Category'     => 'account',
                'ContactEmail' => 'jatekos2@szabadulo.hu',
                'Message'      => 'Helyes jelszóval sem enged be, mindig hibás jelszót ír.',
                'Page'         => '/login',
                'Status'       => 'resolved',
            ],
            [
                'UserID'       => $admin?->UserID,
                'Title'        => 'Teszt bejelentés (admin)',
                'Category'     => 'other',
                'ContactEmail' => null,
                'Message'      => 'Ez egy admin által létrehozott teszt bejelentés a rendszer ellenőrzésére.',
                'Page'         => '/admin',
                'Status'       => 'resolved',
            ],
        ];

        foreach ($reports as $data) {
            Report::create($data);
        }
    }
}
