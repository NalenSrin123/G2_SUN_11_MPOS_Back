<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use App\Models\Otp_token;
use App\Mail\SendOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sending OTP.
     */
    public function test_can_send_otp_to_existing_admin()
    {
        Mail::fake();

        // Create an admin user using the factory
        $user = User::factory()->create([
            'email' => 'test-admin@example.com'
        ]);

        $response = $this->postJson('/api/v1/auth/send-otp', [
            'email' => 'test-admin@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP sent successfully to your email.'
            ]);

        // Assert OTP record is in database
        $this->assertDatabaseHas('otp_token', [
            'admin_id' => $user->id,
            'is_used' => false
        ]);

        // Assert mail was sent
        Mail::assertSent(SendOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test sending OTP fails if user not found.
     */
    public function test_send_otp_fails_if_admin_not_found()
    {
        $response = $this->postJson('/api/v1/auth/send-otp', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Admin account with this email not found.'
            ]);
    }

    /**
     * Test verifying OTP successfully.
     */
    public function test_can_verify_otp_successfully()
    {
        $user = User::factory()->create([
            'email' => 'test-admin@example.com'
        ]);

        $otpRecord = Otp_token::create([
            'admin_id' => $user->id,
            'token' => '999999',
            'is_used' => false,
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test-admin@example.com',
            'token' => '999999'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'OTP verified successfully.'
            ]);

        // Assert OTP is marked as used
        $this->assertTrue((bool) $otpRecord->fresh()->is_used);
    }

    /**
     * Test verifying OTP fails with invalid or expired OTP.
     */
    public function test_verify_otp_fails_if_expired_or_invalid()
    {
        $user = User::factory()->create([
            'email' => 'test-admin@example.com'
        ]);

        // Create expired token
        Otp_token::create([
            'admin_id' => $user->id,
            'token' => '111111',
            'is_used' => false,
            'expires_at' => now()->subMinutes(1),
        ]);

        // Test expired verification
        $responseExpired = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test-admin@example.com',
            'token' => '111111'
        ]);

        $responseExpired->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired OTP token.'
            ]);

        // Test incorrect token verification
        $responseIncorrect = $this->postJson('/api/v1/auth/verify-otp', [
            'email' => 'test-admin@example.com',
            'token' => '222222'
        ]);

        $responseIncorrect->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired OTP token.'
            ]);
    }
}
