<?php

namespace Modules\Auth\Tests\Feature;

use App\Mail\SendPasswordResetMail;
use App\Models\Password_reset_token;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_form_renders(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk()
            ->assertSee('Forgot your password?')
            ->assertSee('Email Password Reset Link');
    }

    public function test_forgot_password_form_sends_reset_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'admin@example.com',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Password reset link sent. Please check your email.');

        $this->assertDatabaseHas('password_reset_token', [
            'admin_id' => $user->id,
            'is_used' => false,
        ]);

        Mail::assertSent(SendPasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_forgot_password_sends_reset_email_and_does_not_create_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'admin@example.com',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Password reset email sent successfully.',
            ]);

        $this->assertDatabaseHas('password_reset_token', [
            'admin_id' => $user->id,
            'is_used' => false,
        ]);

        $this->assertDatabaseMissing('otp_token', [
            'admin_id' => $user->id,
            'purpose' => 'password_reset',
        ]);

        Mail::assertSent(SendPasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_forgot_password_fails_when_admin_email_is_missing(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'missing@example.com',
        ]);

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Admin account with this email not found.',
            ]);
    }

    public function test_reset_password_form_renders_with_email_and_token(): void
    {
        $response = $this->get('/reset-password?token=abc123&email=admin%40example.com');

        $response->assertOk()
            ->assertSee('admin@example.com')
            ->assertSee('abc123')
            ->assertDontSee('Send OTP')
            ->assertDontSee('Verify OTP');
    }

    public function test_reset_password_form_renders_with_token_route_parameter(): void
    {
        $response = $this->get('/reset-password/abc123?email=admin%40example.com');

        $response->assertOk()
            ->assertSee('admin@example.com')
            ->assertSee('abc123');
    }

    public function test_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('old-password'),
        ]);

        $record = Password_reset_token::create([
            'admin_id' => $user->id,
            'token' => 'valid-reset-token',
            'is_used' => false,
            'expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'admin@example.com',
            'token' => 'valid-reset-token',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Password has been reset successfully.',
                'redirect_url' => url('/dashboards'),
            ]);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password_hash));
        $this->assertTrue((bool) $record->fresh()->is_used);
    }

    public function test_reset_password_fails_with_expired_token_and_keeps_password(): void
    {
        $oldHash = Hash::make('old-password');
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password_hash' => $oldHash,
        ]);

        $record = Password_reset_token::create([
            'admin_id' => $user->id,
            'token' => 'expired-reset-token',
            'is_used' => false,
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'admin@example.com',
            'token' => 'expired-reset-token',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid or expired password reset token.',
            ]);

        $this->assertSame($oldHash, $user->fresh()->password_hash);
        $this->assertFalse((bool) $record->fresh()->is_used);
    }
}
