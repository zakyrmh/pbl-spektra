<?php

use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('user can access registration page', function () {
    $response = $this->get(route('register'));
    $response->assertStatus(200);
});

test('user can register with valid data but must verify email', function (string $phoneNumber) {
    $data = [
        'name' => 'Ahmad Fauzi',
        'nik' => '1234567890123456',
        'email' => 'ahmad.fauzi@example.com',
        'phone_number' => $phoneNumber,
        'password' => 'AmanSekali123!',
        'password_confirmation' => 'AmanSekali123!',
    ];

    // Listen to events
    Event::fake([
        Registered::class,
    ]);

    $response = $this->post(route('register.process'), $data);

    // Assert redirect to verification notice
    $response->assertRedirect(route('verification.notice'));

    // Assert Registered event was dispatched
    Event::assertDispatched(Registered::class);

    // Assert user is NOT authenticated (logout is forced or not authenticated)
    expect(Auth::check())->toBeFalse();

    // Assert email is saved in session
    expect(session('unverified_email'))->toBe('ahmad.fauzi@example.com');

    // Assert user exists in database but email is not verified
    $this->assertDatabaseHas('users', [
        'name' => 'Ahmad Fauzi',
        'nik' => '1234567890123456',
        'email' => 'ahmad.fauzi@example.com',
        'no_telp' => $phoneNumber,
        'role' => 'pengunjung',
        'email_verified_at' => null,
    ]);

    // Assert password is encrypted/hashed
    $user = User::where('email', 'ahmad.fauzi@example.com')->first();
    expect(Hash::check('AmanSekali123!', $user->password))->toBeTrue();
})->with([
    'Format 08xx' => '081234567890',
    'Format +628xx' => '+6281234567890',
]);

test('validation fails when field is missing or invalid', function ($field, $value, $customData, $expectedErrorField, $expectedErrorMessage) {
    // Start with default valid data
    $data = [
        'name' => 'Ahmad Fauzi',
        'nik' => '1234567890123456',
        'email' => 'ahmad.fauzi@example.com',
        'phone_number' => '081234567890',
        'password' => 'AmanSekali123!',
        'password_confirmation' => 'AmanSekali123!',
    ];

    if ($field !== null) {
        $data[$field] = $value;
    }

    foreach ($customData as $key => $val) {
        $data[$key] = $val;
    }

    $response = $this->post(route('register.process'), $data);

    $response->assertSessionHasErrors([$expectedErrorField]);

    $errors = session('errors')->get($expectedErrorField);
    expect($errors)->toContain($expectedErrorMessage);
})->with([
    // Nama Lengkap
    ['name', '', [], 'name', 'Nama lengkap wajib diisi.'],
    ['name', str_repeat('a', 256), [], 'name', 'Nama lengkap tidak boleh lebih dari 255 karakter.'],

    // NIK
    ['nik', '', [], 'nik', 'NIK wajib diisi.'],
    ['nik', '12345', [], 'nik', 'NIK harus berupa 16 digit angka.'], // kurang dari 16 digit
    ['nik', '12345678901234567', [], 'nik', 'NIK harus berupa 16 digit angka.'], // lebih dari 16 digit
    ['nik', '12345678901234ab', [], 'nik', 'NIK harus berupa 16 digit angka.'], // non-numeric (mengandung huruf)

    // Email
    ['email', '', [], 'email', 'Alamat email wajib diisi.'],
    ['email', 'invalid-email', [], 'email', 'Format email tidak valid.'],
    ['email', str_repeat('a', 247).'@gmail.com', [], 'email', 'Alamat email tidak boleh lebih dari 255 karakter.'],

    // Nomor HP
    ['phone_number', '', [], 'phone_number', 'Nomor HP wajib diisi.'],
    ['phone_number', '021123456', [], 'phone_number', 'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 karakter).'], // wrong prefix
    ['phone_number', '0812', [], 'phone_number', 'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 karakter).'], // too short
    ['phone_number', '0812345678901234', [], 'phone_number', 'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 karakter).'], // too long (16 chars starting with 08)
    ['phone_number', '+628123456789012', [], 'phone_number', 'Format nomor HP tidak valid (harus diawali 08 atau +628 dan berisi 10-15 karakter).'], // too long (16 chars starting with +628)

    // Password
    ['password', '', ['password_confirmation' => ''], 'password', 'Password wajib diisi.'],
    ['password', '1234567', ['password_confirmation' => '1234567'], 'password', 'Password minimal harus 8 karakter.'], // < 8 characters
    ['password', '12345678!', ['password_confirmation' => '12345678!'], 'password', 'Password harus mengandung minimal satu huruf.'], // no letters
    ['password', 'abcdefgh!', ['password_confirmation' => 'abcdefgh!'], 'password', 'Password harus mengandung minimal satu angka.'], // no numbers
    ['password', 'abcdefg1', ['password_confirmation' => 'abcdefg1'], 'password', 'Password harus mengandung minimal satu simbol.'], // no symbols
    ['password', 'AmanSekali123!', ['password_confirmation' => 'BedaSekali123!'], 'password', 'Konfirmasi password tidak cocok.'], // mismatch
]);

test('registration fails if NIK is already registered', function () {
    // Create an existing user
    User::factory()->create([
        'nik' => '1234567890123456',
        'email' => 'existing.nik@example.com',
    ]);

    $data = [
        'name' => 'Ahmad Baru',
        'nik' => '1234567890123456', // Duplicate NIK
        'email' => 'new.user@example.com',
        'phone_number' => '081234567890',
        'password' => 'S4feP@ssword!',
        'password_confirmation' => 'S4feP@ssword!',
    ];

    $response = $this->post(route('register.process'), $data);

    $response->assertSessionHasErrors(['nik']);

    $errors = session('errors')->get('nik');
    expect($errors)->toContain('NIK sudah terdaftar.');
});

test('registration fails if Email is already registered', function () {
    // Create an existing user
    User::factory()->create([
        'nik' => '1234567890123456',
        'email' => 'existing.email@example.com',
    ]);

    $data = [
        'name' => 'Ahmad Baru',
        'nik' => '6543210987654321',
        'email' => 'existing.email@example.com', // Duplicate email
        'phone_number' => '081234567890',
        'password' => 'S4feP@ssword!',
        'password_confirmation' => 'S4feP@ssword!',
    ];

    $response = $this->post(route('register.process'), $data);

    $response->assertSessionHasErrors(['email']);

    $errors = session('errors')->get('email');
    expect($errors)->toContain('Alamat email sudah terdaftar.');
});

test('unverified user cannot access dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

test('verified user can access dashboard', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
});

test('unverified user is rejected during login', function () {
    $user = User::factory()->create([
        'email' => 'unverified@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
    ]);

    $response = $this->post(route('login.process'), [
        'email' => 'unverified@example.com',
        'password' => 'password123',
    ]);

    // Redirect to verification notice
    $response->assertRedirect(route('verification.notice'));

    // Check email in session for resending
    expect(session('unverified_email'))->toBe('unverified@example.com');

    // Assert that the user is not authenticated
    expect(Auth::check())->toBeFalse();
});

test('verified user can login successfully', function () {
    $user = User::factory()->create([
        'email' => 'verified@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('login.process'), [
        'email' => 'verified@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');
    expect(Auth::check())->toBeTrue();
});

test('user can verify email with valid signed url', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->get($verificationUrl);

    $response->assertRedirect('/dashboard');

    // Assert user is verified in DB
    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();

    // Assert user is auto-logged in
    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

test('user cannot verify email with invalid signed url', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $verificationUrl = route('verification.verify', [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]); // Plain url without signature

    $response = $this->get($verificationUrl);

    $response->assertStatus(401);

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeFalse();
});

test('user can request a resend of verification email', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Illuminate\Support\Facades\Notification::fake();

    $response = $this->withSession(['unverified_email' => $user->email])
        ->post(route('verification.send'));

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, CustomVerifyEmail::class);
});
