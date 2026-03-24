<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    protected $table = 'question_options';
    protected $primaryKey = 'OptionID';
    public $timestamps = false;

    protected $fillable = [
        'QuestionID',
        'OptionText',
        'IsCorrect',
    ];

    protected function casts(): array
    {
        return [
            'IsCorrect' => 'boolean',
        ];
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'QuestionID', 'QuestionID');
    }
}
