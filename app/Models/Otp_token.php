<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp_token extends Model
{
    protected $table = 'otp_token';

    public const UPDATED_AT = null;

    protected $fillable = [
        'admin_id',
        'token',
        'is_used',
        'expires_at',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
