<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserMoney;
use App\Models\LeaderboardEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'Username'     => 'admin',
                'Email'        => 'admin@szabadulo.hu',
                'PasswordHash' => Hash::make('Admin1234'),
                'IsAdmin'      => true,
                'IsActive'     => true,
            ],
            [
                'Username'     => 'jatekos1',
                'Email'        => 'jatekos1@szabadulo.hu',
                'PasswordHash' => Hash::make('Jatekos1234'),
                'IsAdmin'      => false,
                'IsActive'     => true,
            ],
            [
                'Username'     => 'jatekos2',
                'Email'        => 'jatekos2@szabadulo.hu',
                'PasswordHash' => Hash::make('Jatekos1234'),
                'IsAdmin'      => false,
                'IsActive'     => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            UserMoney::create([
                'UserID' => $user->UserID,
                'Amount' => 500,
            ]);

            LeaderboardEntry::create([
                'UserID'          => $user->UserID,
                'Score'           => 0,
                'LevelsCompleted' => 0,
                'TimeTotal'       => 0,
                'HintsUsed'       => 0,
            ]);
        }
    }
}
