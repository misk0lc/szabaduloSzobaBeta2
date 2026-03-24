<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hint extends Model
{
    protected $table = 'hints';
    protected $primaryKey = 'HintID';
    public $timestamps = false;

    protected $fillable = [
        'QuestionID',
        'HintText',
        'Cost',
        'HintOrder',
    ];

    protected function casts(): array
    {
        return [
            'Cost'      => 'integer',
            'HintOrder' => 'integer',
        ];
    }

    // N:1 → Question (CASCADE törlés a migráción van)
    public function question()
    {
        return $this->belongsTo(Question::class, 'QuestionID', 'QuestionID');
    }
}
