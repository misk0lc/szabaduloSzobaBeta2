<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    protected $table = 'leaderboard';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'Score',
        'LevelsCompleted',
        'TimeTotal',
        'HintsUsed',
    ];

    protected function casts(): array
    {
        return [
            'Score'          => 'integer',
            'LevelsCompleted'=> 'integer',
            'TimeTotal'      => 'integer',
            'HintsUsed'      => 'integer',
        ];
    }

    // 1:1 → User (CASCADE törlés a migráción van)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
