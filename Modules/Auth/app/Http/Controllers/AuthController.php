<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp_token;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
                'message' => 'Admin user with this email not found.'
            ], 404);
        }

        // Generate a 6-digit numeric OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Create database record
        Otp_token::create([
            'admin_id' => $user->id,
            'token' => $otp,
            'is_used' => false,
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
                'message' => 'Admin user with this email not found.'
            ], 404);
        }

        // Find the active, unused OTP token
        $otpRecord = Otp_token::where('admin_id', $user->id)
            ->where('token', $request->token)
            ->where('is_used', false)
            ->where('expires_at', '>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->first();

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

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }
}
