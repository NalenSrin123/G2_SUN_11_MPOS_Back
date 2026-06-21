<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp_token;
use App\Models\Password_reset_token;
use App\Mail\SendOtpMail;
use App\Mail\SendPasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Send OTP verification email to the user.
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Admin account with this email not found.'
            ], 404);
        }

        // Generate a 6-digit numeric OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Purpose can be 'login' or 'password_reset'
        $purpose = $request->input('purpose', 'login');

        // Create database record
        Otp_token::create([
            'admin_id' => $user->id,
            'token' => $otp,
            'is_used' => false,
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes(2),
        ]);

        try {
            // Dispatch email via configured Gmail SMTP
            Mail::to($user->email)->send(new SendOtpMail($otp));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email. Please check your SMTP configuration.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email.'
        ]);
    }

    /**
     * Login with email and password, then send OTP if credentials are valid.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password. Please try again.'
            ], 401);
        }

        try {
            $this->sendOtpToUser($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP email. Please check your SMTP configuration.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful. OTP sent to your email.'
        ]);
    }

    /**
     * Create and email OTP for the given admin user.
     */
    protected function sendOtpToUser(User $user)
    {
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        Otp_token::create([
            'admin_id' => $user->id,
            'token' => $otp,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(2),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    /**
     * Verify the OTP token submitted by the user.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'send_reset' => 'sometimes|boolean',
            'purpose' => 'sometimes|string|in:login,password_reset',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Admin account with this email not found.'
            ], 404);
        }

        // Find the active, unused OTP token. If purpose provided, match it.
        $query = Otp_token::where('admin_id', $user->id)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now());

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->input('purpose'));
        }

        $otpRecord = $query->orderBy('id', 'desc')->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP token.'
            ], 400);
        }

        // Mark the token as used
        $otpRecord->update([
            'is_used' => true
        ]);

        // If OTP record purpose is password_reset, or client explicitly requested email, create a password-reset token.
        $resetUrl = null;
        $isPasswordResetFlow = ($otpRecord->purpose === 'password_reset') || ($request->input('purpose') === 'password_reset');
        if ($isPasswordResetFlow || $request->boolean('send_reset')) {
            $resetToken = Str::random(64);

            Password_reset_token::create([
                'admin_id' => $user->id,
                'token' => $resetToken,
                'is_used' => false,
                'expires_at' => Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
            ]);

            // If client asked to send the email, keep existing behavior.
            if ($request->boolean('send_reset')) {
                try {
                    Mail::to($user->email)->send(new SendPasswordResetMail($user, $resetToken));
                    // Also send an OTP to the admin when sending the reset email
                    try {
                        $otp = sprintf("%06d", mt_rand(100000, 999999));
                        Otp_token::create([
                            'admin_id' => $user->id,
                            'token' => $otp,
                            'is_used' => false,
                            'purpose' => 'password_reset',
                            'expires_at' => Carbon::now()->addMinutes(2),
                        ]);
                        Mail::to($user->email)->send(new SendOtpMail($otp));
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => true,
                            'message' => 'OTP verified and password reset email sent, but failed to send OTP. Check SMTP settings for OTP.',
                            'otp_error' => $e->getMessage(),
                        ]);
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP verified but failed to send password reset email. Please check SMTP configuration.',
                        'error' => $e->getMessage()
                    ], 500);
                }
            } else {
                // Do not send email; return the reset form URL so the client can navigate to it.
                $resetUrl = rtrim(config('app.url'), '/') . '/reset-password?token=' . $resetToken . '&email=' . urlencode($user->email);
                // If this is an HTML request (not expecting JSON), redirect to the reset form so Postman/browser opens it.
                if (! $request->wantsJson()) {
                    return redirect($resetUrl);
                }
            }
        }

        $response = [
            'success' => true,
            'message' => 'OTP verified successfully.',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ];

        if ($resetUrl) {
            $response['reset_url'] = $resetUrl;
        }

        return response()->json($response);
    }

    /**
     * Send password reset email with a token.
     */
    public function sendPasswordResetForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => "We can't find a user with that email address."])
                ->withInput();
        }

        $token = $this->createPasswordResetToken($user);

        try {
            Mail::to($user->email)->send(new SendPasswordResetMail($user, $token));
        } catch (\Exception $e) {
            return back()
                ->withErrors(['email' => 'Failed to send password reset email. Please check your SMTP configuration.'])
                ->withInput();
        }

        return back()->with('status', 'Password reset link sent. Please check your email.');
    }

    public function sendPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Admin account with this email not found.'
            ], 404);
        }

        $token = $this->createPasswordResetToken($user);

        try {
            Mail::to($user->email)->send(new SendPasswordResetMail($user, $token));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email. Please check your SMTP configuration.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent successfully.'
        ]);
    }

    /**
     * Reset the user's password using token.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Admin account with this email not found.'
            ], 404);
        }

        $record = Password_reset_token::where('admin_id', $user->id)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired password reset token.'
            ], 400);
        }

        // Update the user's password
        $user->update([
            'password_hash' => Hash::make($request->password),
        ]);

        // Mark token as used
        $record->update(['is_used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.',
            'redirect_url' => url('/dashboards'),
        ]);
    }

    private function createPasswordResetToken(User $user): string
    {
        $token = Str::random(64);

        Password_reset_token::create([
            'admin_id' => $user->id,
            'token' => $token,
            'is_used' => false,
            'expires_at' => Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
        ]);

        return $token;
    }
}
