<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'QuestionID';
    public $timestamps = false;

    protected $fillable = [
        'LevelID',
        'QuestionText',
        'CorrectAnswer',
        'RewardDigit',
        'MoneyReward',
        'PositionX',
        'PositionY',
    ];

    protected function casts(): array
    {
        return [
            'RewardDigit' => 'integer',
            'MoneyReward' => 'integer',
            'PositionX'   => 'integer',
            'PositionY'   => 'integer',
        ];
    }

    // N:1 → Level (CASCADE törlés a migráción van)
    public function level()
    {
        return $this->belongsTo(Level::class, 'LevelID', 'LevelID');
    }

    // 1:N → Hints
    public function hints()
    {
        return $this->hasMany(Hint::class, 'QuestionID', 'QuestionID');
    }

    // 1:N → Options
    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'QuestionID', 'QuestionID');
    }

    // 1:N → UserAnswers
    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'QuestionID', 'QuestionID');
    }
}
