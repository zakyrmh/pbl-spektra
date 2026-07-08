<?php

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guest cannot access profile page', function () {
    $response = test()->get(route('profile.edit'));
    $response->assertRedirect(route('login'));
});

test('logged in user can access profile page', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '081234567890',
        'role' => 'pengunjung',
    ]);

    $response = test()->actingAs($user)->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertSee('John Doe');
    $response->assertSee('081234567890');
    $response->assertSee('Nomor Induk Kependudukan (NIK)');
});

test('validation fails for invalid profile data', function () {
    /** @var User $user */
    $user = User::factory()->create(['role' => 'pengunjung']);

    $response = test()->actingAs($user)->put(route('profile.update'), [
        'name' => '',
        'phone_number' => '12345',
    ]);

    $response->assertSessionHasErrors(['name', 'phone_number']);
});

test('validation fails for wrong image type and oversized files', function () {
    /** @var User $user */
    $user = User::factory()->create(['role' => 'pengunjung']);
    Storage::fake('public');

    $fakeTextFile = UploadedFile::fake()->create('document.txt', 100);
    $largeImageFile = UploadedFile::fake()->create('large.png', 3000, 'image/png');

    $response = test()->actingAs($user)->put(route('profile.update'), [
        'name' => 'New Name',
        'phone_number' => '081234567891',
        'avatar' => $fakeTextFile,
        'ktp_photo' => $largeImageFile,
    ]);

    $response->assertSessionHasErrors(['avatar', 'ktp_photo']);
});

test('user can update profile and upload photos successfully', function () {
    /** @var User $user */
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone_number' => '081234567890',
        'role' => 'pengunjung',
    ]);

    Storage::fake('public');

    $avatar = UploadedFile::fake()->image('avatar.jpg');
    $ktp = UploadedFile::fake()->image('ktp.png');

    $response = test()->actingAs($user)->put(route('profile.update'), [
        'name' => 'New Name',
        'phone_number' => '089876543210',
        'avatar' => $avatar,
        'ktp_photo' => $ktp,
    ]);

    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('success', 'Data profil berhasil diperbarui.');

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->phone_number)->toBe('089876543210');
    expect($user->avatar_path)->not->toBeNull();
    expect($user->ktp_photo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($user->avatar_path);
    Storage::disk('public')->assertExists($user->ktp_photo_path);

    // Menggunakan query() agar Intelephense tidak menduga fungsi internal php queue
    $log = ActivityLog::query()->where('event', 'user_updated')->first();
    expect($log)->not->toBeNull();
    expect($log->causer_id)->toBe($user->id);
    expect($log->description)->toContain("Data pengguna 'New Name'");

    $properties = $log->properties;
    expect($properties['before']['name'])->toBe('Old Name');
    expect($properties['after']['name'])->toBe('New Name');
});

test('old avatar and ktp photo are deleted from storage when new ones are uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'role' => 'pengunjung',
        'avatar_path' => 'avatars/old-avatar.jpg',
        'ktp_photo_path' => 'ktp_photos/old-ktp.jpg',
    ]);

    // Put mock files in storage to delete
    Storage::disk('public')->put('avatars/old-avatar.jpg', 'old content');
    Storage::disk('public')->put('ktp_photos/old-ktp.jpg', 'old content');

    Storage::disk('public')->assertExists('avatars/old-avatar.jpg');
    Storage::disk('public')->assertExists('ktp_photos/old-ktp.jpg');

    $newAvatar = UploadedFile::fake()->image('new-avatar.jpg');
    $newKtp = UploadedFile::fake()->image('new-ktp.png');

    $response = $this->actingAs($user)->put(route('profile.update'), [
        'name' => 'Name',
        'phone_number' => '081234567890',
        'avatar' => $newAvatar,
        'ktp_photo' => $newKtp,
    ]);

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();

    // Verify old files are deleted
    Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
    Storage::disk('public')->assertMissing('ktp_photos/old-ktp.jpg');

    // Verify new files exist
    Storage::disk('public')->assertExists($user->avatar_path);
    Storage::disk('public')->assertExists($user->ktp_photo_path);
});
