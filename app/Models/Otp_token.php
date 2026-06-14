<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp_token extends Model
{
    //
    protected $table = 'otp_token';
    protected $fillable = [
        'admin_id',
        'token',    
        'is_used',
        'expires_at',
    ];
}
