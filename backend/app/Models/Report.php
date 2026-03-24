<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'ReportID';

    protected $fillable = [
        'UserID',
        'Title',
        'Message',
        'Page',
        'Status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
