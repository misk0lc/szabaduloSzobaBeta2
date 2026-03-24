<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';
    protected $primaryKey = 'ProgressID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'LevelID',
        'Completed',
        'TimeSpent',
        'CompletedAt',
    ];

    protected function casts(): array
    {
        return [
            'Completed'   => 'boolean',
            'TimeSpent'   => 'integer',
            'CompletedAt' => 'datetime',
        ];
    }

    // N:1 → User (CASCADE törlés a migráción van)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    // N:1 → Level (CASCADE törlés a migráción van)
    public function level()
    {
        return $this->belongsTo(Level::class, 'LevelID', 'LevelID');
    }
}
