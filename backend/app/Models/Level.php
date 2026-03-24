<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'levels';
    protected $primaryKey = 'LevelID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Description',
        'OrderNumber',
        'IsActive',
        'BackgroundUrl',
    ];

    protected function casts(): array
    {
        return [
            'IsActive'  => 'boolean',
            'CreatedAt' => 'datetime',
        ];
    }

    // 1:N → Questions
    public function questions()
    {
        return $this->hasMany(Question::class, 'LevelID', 'LevelID');
    }

    // 1:N → UserProgress
    public function userProgress()
    {
        return $this->hasMany(UserProgress::class, 'LevelID', 'LevelID');
    }
}
