<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('guest can access login page', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertViewIs('auth.login');
});

test('user can log in with valid credentials', function () {
    $password = 'SecurePassword123!';
    $user = User::factory()->create([
        'email' => 'staff@sawahlunto.go.id',
        'password' => Hash::make($password),
        'role' => 'admin_fo',
        'last_login_at' => null,
    ]);

    expect($user->last_login_at)->toBeNull();

    $response = $this->post(route('login.process'), [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertRedirect(route('admin_fo.dashboard'));

    // Assert authentication
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);

    // Assert last_login_at updated
    $user->refresh();
    expect($user->last_login_at)->not->toBeNull();

    // Assert audit trail logged
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $user->id,
        'subject_id' => $user->id,
        'subject_type' => User::class,
        'event' => 'login',
        'description' => "Pengguna '{$user->name}' berhasil masuk ke sistem.",
    ]);
});

test('user can log in with valid NIK', function () {
    $password = 'SecurePassword123!';
    $user = User::factory()->create([
        'nik' => '1234567890123456',
        'email' => 'staff@sawahlunto.go.id',
        'password' => Hash::make($password),
        'role' => 'admin_fo',
        'last_login_at' => null,
    ]);

    expect($user->last_login_at)->toBeNull();

    $response = $this->post(route('login.process'), [
        'email' => $user->nik,
        'password' => $password,
    ]);

    $response->assertRedirect(route('admin_fo.dashboard'));

    // Assert authentication
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);

    // Assert last_login_at updated
    $user->refresh();
    expect($user->last_login_at)->not->toBeNull();
});

test('user cannot log in with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'staff@sawahlunto.go.id',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $response = $this->from(route('login'))->post(route('login.process'), [
        'email' => $user->email,
        'password' => 'WrongPassword123!',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['email']);
    expect(session('errors')->first('email'))->toBe('NIK/Email atau password salah.');
    expect(Auth::check())->toBeFalse();
});

test('login validation fails when fields are missing or invalid', function ($email, $password, $expectedErrorField) {
    $response = $this->from(route('login'))->post(route('login.process'), [
        'email' => $email,
        'password' => $password,
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([$expectedErrorField]);
})->with([
    ['', 'password', 'email'],
    ['not-an-email', 'password', 'email'],
    ['staff@sawahlunto.go.id', '', 'password'],
]);

test('login is rate limited after 5 failed attempts', function () {
    $email = 'staff@sawahlunto.go.id';

    // Perform 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        $response = $this->post(route('login.process'), [
            'email' => $email,
            'password' => 'WrongPassword!',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    // The 6th attempt should trigger the rate limiter block
    $response = $this->from(route('login'))->post(route('login.process'), [
        'email' => $email,
        'password' => 'WrongPassword!',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors(['email']);
    expect(session('errors')->first('email'))->toContain('Terlalu banyak percobaan masuk');
});

test('authenticated user can log out', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect('/login');
    expect(Auth::check())->toBeFalse();

    // Assert audit trail logged for logout
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $user->id,
        'subject_id' => $user->id,
        'subject_type' => User::class,
        'event' => 'logout',
        'description' => "Pengguna '{$user->name}' berhasil keluar dari sistem.",
    ]);
});

test('guest can view forgot password page', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
    $response->assertViewIs('auth.forgot-password');
});

test('user can request password reset link', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'warga@sawahlunto.go.id',
    ]);

    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => $user->email,
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('status');

    // In Laravel, standard reset uses notification
    Notification::assertSentTo($user, ResetPassword::class);
});

test('requesting password reset fails for non-existent email', function () {
    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'doesnotexist@sawahlunto.go.id',
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHasErrors(['email']);
});

test('user can view reset password form with token', function () {
    $token = 'dummy-reset-token';
    $email = 'warga@sawahlunto.go.id';

    $response = $this->get(route('password.reset', [
        'token' => $token,
        'email' => $email,
    ]));

    $response->assertStatus(200);
    $response->assertViewIs('auth.reset-password');
    $response->assertViewHas('token', $token);
    $response->assertViewHas('email', $email);
});

test('user can reset password with valid token', function () {
    Event::fake();

    $user = User::factory()->create([
        'email' => 'warga@sawahlunto.go.id',
        'password' => Hash::make('OldPassword123!'),
    ]);

    // Generate a valid token for the user
    $token = Password::createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    // Assert password changed
    $user->refresh();
    expect(Hash::check('NewPassword123!', $user->password))->toBeTrue();

    // Assert event fired
    Event::assertDispatched(PasswordReset::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('password reset fails with invalid token or mismatched passwords', function ($tokenModifier, $password, $confirmPassword, $expectedErrorField) {
    $user = User::factory()->create([
        'email' => 'warga@sawahlunto.go.id',
        'password' => Hash::make('OldPassword123!'),
    ]);

    $token = Password::createToken($user);
    $targetToken = $tokenModifier ? $tokenModifier : $token;

    $response = $this->from(route('password.reset', ['token' => $targetToken]))
        ->post(route('password.update'), [
            'token' => $targetToken,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $confirmPassword,
        ]);

    $response->assertRedirect(route('password.reset', ['token' => $targetToken]));
    $response->assertSessionHasErrors([$expectedErrorField]);

    // Password should remain unchanged
    $user->refresh();
    expect(Hash::check('OldPassword123!', $user->password))->toBeTrue();
})->with([
    ['invalid-token', 'NewPassword123!', 'NewPassword123!', 'email'],
    [null, 'NewPassword123!', 'DifferentPassword123!', 'password'],
    [null, 'short', 'short', 'password'], // fails validation (min length)
    [null, 'NoSymbol123', 'NoSymbol123', 'password'], // fails validation (missing symbol)
    [null, 'NoNumber!!!', 'NoNumber!!!', 'password'], // fails validation (missing number)
    [null, '12345678!!!', '12345678!!!', 'password'], // fails validation (missing letters)
]);
