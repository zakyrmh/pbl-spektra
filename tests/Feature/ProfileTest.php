<?php

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guest cannot access profile page', function () {
    $response = $this->get(route('profile.edit'));
    $response->assertRedirect(route('login'));
});

test('logged in user can access profile page', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '081234567890',
        'role' => 'pengunjung',
    ]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertSee('081234567890');
    $response->assertSee('Nomor Induk Kependudukan (NIK)');
});

test('validation fails for invalid profile data', function () {
    $user = User::factory()->create(['role' => 'pengunjung']);

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'name' => '', // empty name
        'phone_number' => '12345', // invalid phone number format
    ]);

    $response->assertSessionHasErrors(['name', 'phone_number']);
});

test('validation fails for wrong image type and oversized files', function () {
    $user = User::factory()->create(['role' => 'pengunjung']);
    Storage::fake('public');

    // Create a fake txt file and name it as image
    $fakeTextFile = UploadedFile::fake()->create('document.txt', 100);
    // Create an oversized image (3MB)
    $largeImageFile = UploadedFile::fake()->create('large.png', 3000, 'image/png');

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'name' => 'New Name',
        'phone_number' => '081234567891',
        'avatar' => $fakeTextFile,
        'ktp_photo' => $largeImageFile,
    ]);

    $response->assertSessionHasErrors(['avatar', 'ktp_photo']);
});

test('user can update profile and upload photos successfully', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone_number' => '081234567890',
        'role' => 'pengunjung',
    ]);

    Storage::fake('public');

    $avatar = UploadedFile::fake()->image('avatar.jpg');
    $ktp = UploadedFile::fake()->image('ktp.png');

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'name' => 'New Name',
        'phone_number' => '089876543210',
        'avatar' => $avatar,
        'ktp_photo' => $ktp,
    ]);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'Data profil berhasil diperbarui.');

    // Verify user is updated in database
    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->phone_number)->toBe('089876543210');
    expect($user->avatar_path)->not->toBeNull();
    expect($user->ktp_photo_path)->not->toBeNull();

    // Verify files exist in storage fakes
    Storage::disk('public')->assertExists($user->avatar_path);
    Storage::disk('public')->assertExists($user->ktp_photo_path);

    // Verify audit logs were written
    $log = ActivityLog::where('event', 'user_updated')->first();
    expect($log)->not->toBeNull();
    expect($log->causer_id)->toBe($user->id);
    expect($log->description)->toContain("Data pengguna 'New Name'");

    // Check that properties contain before and after states
    $properties = $log->properties;
    expect($properties['before']['name'])->toBe('Old Name');
    expect($properties['after']['name'])->toBe('New Name');
});
