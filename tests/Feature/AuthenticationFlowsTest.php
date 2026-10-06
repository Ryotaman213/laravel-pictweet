<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_authentication_forms(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('name="email"', false);
        $this->get(route('register'))->assertOk()->assertSee('name="nickname"', false);
        $this->get(route('password.request'))->assertOk()->assertSee('name="email"', false);
    }

    public function test_registration_creates_a_hashed_password_and_starts_a_session(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Aki Tanaka',
            'nickname' => 'aki',
            'email' => 'aki@example.test',
            'password' => 'safe-password-123',
            'password_confirmation' => 'safe-password-123',
        ]);

        $user = User::where('email', 'aki@example.test')->firstOrFail();

        $response->assertRedirect(route('tweets.index'));
        $this->assertTrue(Hash::check('safe-password-123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_email_login_and_logout_regenerate_session_state(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.test',
            'password' => Hash::make('correct-password'),
        ]);
        $previousId = $this->app['session']->driver()->getId();

        $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'correct-password',
            'remember' => '1',
        ])->assertRedirect(route('tweets.index'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($previousId, $this->app['session']->driver()->getId());

        $previousToken = $this->app['session']->token();
        $this->post(route('logout'))->assertRedirect(route('tweets.index'));

        $this->assertGuest();
        $this->assertNotSame($previousToken, $this->app['session']->token());
    }

    public function test_failed_and_malformed_login_requests_do_not_authenticate(): void
    {
        $this->from(route('login'))->post(route('login'), [
            'email' => 'missing@example.test',
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->from(route('login'))->post(route('login'), [
            'email' => ['not-an-email'],
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_attempts_are_rate_limited_by_ip_and_email(): void
    {
        RateLimiter::clear('login-ip:127.0.0.1');
        RateLimiter::clear('login-user:'.hash('sha256', 'locked@example.test').'|127.0.0.1');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login'), [
                'email' => 'locked@example.test',
                'password' => 'wrong-password',
            ])->assertRedirect(route('login'));
        }

        $this->post(route('login'), [
            'email' => 'locked@example.test',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_password_reset_sends_a_link_and_accepts_a_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset@example.test',
            'password' => Hash::make('old-password'),
        ]);

        $this->from(route('password.request'))->post(route('password.email'), [
            'email' => $user->email,
        ])->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
        $notification = Notification::sent($user, ResetPassword::class)->first();

        $this->post(route('password.update'), [
            'email' => $user->email,
            'token' => $notification->token,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));

        $this->from(route('password.request'))->post(route('password.update'), [
            'email' => $user->email,
            'token' => $notification->token,
            'password' => 'another-password-123',
            'password_confirmation' => 'another-password-123',
        ])->assertRedirect(route('password.request'))->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'new-password-123',
        ])->assertRedirect(route('tweets.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_reset_response_does_not_reveal_unknown_emails(): void
    {
        Notification::fake();

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'unknown@example.test',
        ])->assertRedirect(route('password.request'));

        $this->assertSame(
            'If an account exists for that email, we have sent a password reset link.',
            $response->getSession()->get('status')
        );
        Notification::assertNothingSent();
    }

    public function test_malformed_password_reset_email_is_validated_without_error(): void
    {
        $this->from(route('password.request'))->post(route('password.email'), [
            'email' => ['not-an-email'],
        ])->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');
    }

    public function test_invalid_password_reset_token_does_not_change_the_password(): void
    {
        $user = User::factory()->create([
            'email' => 'reset-invalid@example.test',
            'password' => Hash::make('keep-this-password'),
        ]);

        $this->from(route('password.request'))->post(route('password.update'), [
            'email' => $user->email,
            'token' => 'not-a-valid-reset-token',
            'password' => 'attacker-password-123',
            'password_confirmation' => 'attacker-password-123',
        ])->assertRedirect(route('password.request'))->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('keep-this-password', $user->fresh()->password));
    }
}
