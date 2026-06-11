<?php

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

// Fungsi pembantu untuk setup data awal (menggantikan beforeEach)
function createTestData()
{
    /** @var User $superAdmin */
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $foAdmin */
    $foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // Seed default settings values
    Setting::create([
        'key' => 'app_name',
        'value' => 'Mal Pelayanan Publik Sawahlunto',
        'description' => 'Nama resmi aplikasi',
    ]);

    Setting::create([
        'key' => 'app_logo',
        'value' => 'images/logo.png',
        'description' => 'Path logo',
    ]);

    Setting::create([
        'key' => 'maintenance_mode',
        'value' => '0',
        'description' => 'Mode pemeliharaan',
    ]);

    Setting::create([
        'key' => 'marquee_text',
        'value' => 'Selamat Datang',
        'description' => 'Teks berjalan',
    ]);

    Setting::create([
        'key' => 'marquee_active',
        'value' => '1',
        'description' => 'Marquee aktif',
    ]);

    Setting::create([
        'key' => 'reverb_host',
        'value' => '127.0.0.1',
        'description' => 'WebSocket host',
    ]);

    Setting::create([
        'key' => 'reverb_port',
        'value' => '8080',
        'description' => 'WebSocket port',
    ]);

    Setting::create([
        'key' => 'reverb_scheme',
        'value' => 'http',
        'description' => 'WebSocket scheme',
    ]);

    Setting::create([
        'key' => 'websocket_enabled',
        'value' => '1',
        'description' => 'WebSocket status',
    ]);

    Cache::flush();

    return compact('superAdmin', 'foAdmin');
}

test('guests are redirected to login when accessing system settings routes', function () {
    // Jalankan seeding awal tanpa mengambil variabel return jika tidak digunakan
    createTestData();

    test()->get(route('admin.settings.index'))->assertRedirect(route('login'));
    test()->put(route('admin.settings.update'), [])->assertRedirect(route('login'));
});

test('unauthorized roles cannot access system settings routes (403)', function () {
    $data = createTestData();

    test()->actingAs($data['foAdmin'])->get(route('admin.settings.index'))->assertStatus(403);
    test()->actingAs($data['foAdmin'])->put(route('admin.settings.update'), [])->assertStatus(403);
});

test('super admin can view the system settings form page', function () {
    $data = createTestData();

    $response = test()->actingAs($data['superAdmin'])->get(route('admin.settings.index'));
    $response->assertStatus(200);
    $response->assertSee('Pengaturan Sistem');
    $response->assertSee('Nama Aplikasi / Instansi');
    $response->assertSee('Mal Pelayanan Publik Sawahlunto');
});

test('super admin can successfully update settings and clear cache', function () {
    $data = createTestData();

    // Prime the cache first
    expect(Setting::getVal('app_name'))->toBe('Mal Pelayanan Publik Sawahlunto');

    $response = test()->actingAs($data['superAdmin'])
        ->from(route('admin.settings.index'))
        ->put(route('admin.settings.update'), [
            'app_name' => 'MPP Sawahlunto Baru',
            'app_logo' => 'images/logo_baru.png',
            'maintenance_mode' => '1',
            'marquee_text' => 'Teks berjalan baru',
            'marquee_active' => '0',
            'reverb_host' => 'localhost',
            'reverb_port' => 6001,
            'reverb_scheme' => 'https',
            'websocket_enabled' => '0',
        ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHas('success');

    // Verify database updates
    test()->assertDatabaseHas('settings', [
        'key' => 'app_name',
        'value' => 'MPP Sawahlunto Baru',
    ]);
    test()->assertDatabaseHas('settings', [
        'key' => 'marquee_text',
        'value' => 'Teks berjalan baru',
    ]);

    // Verify cache has been updated
    expect(Setting::getVal('app_name'))->toBe('MPP Sawahlunto Baru');
    expect(Setting::getVal('reverb_port'))->toBe('6001');

    // Verify activity log is written
    $log = ActivityLog::query()->where('event', 'settings_updated')->first();
    expect($log)->not->toBeNull();
    expect($log->causer_id)->toBe($data['superAdmin']->id);
    expect($log->description)->toContain('pengaturan sistem');
});

test('validation rejects invalid settings formats', function () {
    $data = createTestData();

    $response = test()->actingAs($data['superAdmin'])
        ->from(route('admin.settings.index'))
        ->put(route('admin.settings.update'), [
            'app_name' => '',
            'app_logo' => '',
            'maintenance_mode' => '2',
            'marquee_text' => '',
            'marquee_active' => '3',
            'reverb_host' => '',
            'reverb_port' => 'invalid-port',
            'reverb_scheme' => 'ftp',
            'websocket_enabled' => '9',
        ]);

    $response->assertRedirect(route('admin.settings.index'));
    $response->assertSessionHasErrors([
        'app_name', 'app_logo', 'maintenance_mode', 'marquee_text',
        'marquee_active', 'reverb_host', 'reverb_port', 'reverb_scheme', 'websocket_enabled',
    ]);
});
