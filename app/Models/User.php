<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerifyEmailMail;

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

    /**
     * Override the default verification notification to send a custom mailable
     * which contains a signed verification URL that points to our route.
     */
    public function sendEmailVerificationNotification()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $this->id, 'hash' => sha1($this->email)]
        );

        Mail::to($this->email)->send(new SendVerifyEmailMail($this, $url));
    }
}
