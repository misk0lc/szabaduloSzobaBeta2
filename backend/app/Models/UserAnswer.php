<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    protected $table = 'user_answers';
    protected $primaryKey = 'AnswerID';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'QuestionID',
        'GivenAnswer',
        'IsCorrect',
        'AnsweredAt',
    ];

    protected function casts(): array
    {
        return [
            'IsCorrect'  => 'boolean',
            'AnsweredAt' => 'datetime',
        ];
    }

    // N:1 → User (CASCADE törlés a migráción van)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    // N:1 → Question (CASCADE törlés a migráción van)
    public function question()
    {
        return $this->belongsTo(Question::class, 'QuestionID', 'QuestionID');
    }
}
