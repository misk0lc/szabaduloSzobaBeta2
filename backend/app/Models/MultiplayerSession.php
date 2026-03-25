<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultiplayerSession extends Model
{
    protected $fillable = ['LevelID', 'Status', 'SolvedQuestions'];

    protected $casts = [
        'SolvedQuestions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'multiplayer_session_users', 'SessionID', 'UserID')
            ->withPivot('IsReady')
            ->withTimestamps();
    }

    public function level()
    {
        return $this->belongsTo(Level::class, 'LevelID', 'LevelID');
    }
}
