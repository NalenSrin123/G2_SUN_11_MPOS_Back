<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Password_reset_token extends Model
{
    //
    protected $table = 'password_reset_token';
    protected $fillable = [
        'admin_id',
        'token',
        'is_used',
        'expires_at',
    ];
}
