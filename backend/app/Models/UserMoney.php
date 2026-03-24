<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMoney extends Model
{
    protected $table = 'user_money';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['UserID', 'Amount'];

    // 1:1 → User (CASCADE törlés a migráción van)
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
