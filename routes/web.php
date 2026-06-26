<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Forgot password form and submit handler.
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetForm'])->name('password.email');

// Reset password form shown to users who click the emailed link.
Route::get('/reset-password', [AuthController::class, 'showResetForm']);
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Password_reset_token;
use App\Models\User;

// Override the default verification link handler so clicks create a password-reset token
// and redirect the user to the reset form with a token and email already attached.
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    if (! $request->hasValidSignature()) {
        abort(403, 'Invalid or expired verification link.');
    }

    $user = User::find($id);
    if (! $user) {
        abort(404);
    }

    // Create a password reset token record
    $token = Str::random(64);
    Password_reset_token::create([
        'admin_id' => $user->id,
        'token' => $token,
        'is_used' => false,
        'expires_at' => Carbon::now()->addMinutes(2),
    ]);

    $resetUrl = rtrim(config('app.url'), '/') . '/reset-password/' . $token . '?email=' . urlencode($user->email);

    return redirect($resetUrl);
})->name('verification.verify');
