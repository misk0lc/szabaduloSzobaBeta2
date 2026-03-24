<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'UserID';

    public $timestamps = false;

    const CREATED_AT = 'CreatedAt';

    protected $fillable = [
        'Username',
        'Email',
        'PasswordHash',
        'IsAdmin',
        'IsActive',
    ];

    protected $hidden = [
        'PasswordHash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'PasswordHash' => 'hashed',
            'IsAdmin'      => 'boolean',
            'IsActive'     => 'boolean',
        ];
    }

    // Sanctum a PasswordHash mezőt használja
    public function getAuthPassword()
    {
        return $this->PasswordHash;
    }

    public function money()
    {
        return $this->hasOne(UserMoney::class, 'UserID', 'UserID');
    }

    // 1:N → UserProgress
    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'UserID', 'UserID');
    }

    // 1:N → UserAnswers
    public function answers()
    {
        return $this->hasMany(UserAnswer::class, 'UserID', 'UserID');
    }

    // 1:1 → LeaderboardEntry
    public function leaderboard()
    {
        return $this->hasOne(LeaderboardEntry::class, 'UserID', 'UserID');
    }
}
