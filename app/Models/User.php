<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'admins';
    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
    ];

    public $timestamps = true;

    public function otpTokens()
    {
        return $this->hasMany(Otp_token::class, 'admin_id');
    }

    public function passwordResetTokens()
    {
        return $this->hasMany(Password_reset_token::class, 'admin_id');
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
