<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Super Admin actor
    $this->superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    // Target user (the one whose sessions will be managed)
    $this->targetUser = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // Another user with non-super-admin role
    $this->unauthorizedUser = User::factory()->create([
        'role' => 'admin_fo',
    ]);
});

test('guest is redirected to login when accessing session management routes', function () {
    $this->get(route('users.sessions.index', $this->targetUser->id))
        ->assertRedirect(route('login'));

    $this->delete(route('users.sessions.destroy', [$this->targetUser->id, 'session-id']))
        ->assertRedirect(route('login'));

    $this->delete(route('users.sessions.destroy-all', $this->targetUser->id))
        ->assertRedirect(route('login'));
});

test('unauthorized users cannot access session management routes', function () {
    $this->actingAs($this->unauthorizedUser);

    $this->get(route('users.sessions.index', $this->targetUser->id))
        ->assertStatus(403);

    $this->delete(route('users.sessions.destroy', [$this->targetUser->id, 'session-id']))
        ->assertStatus(403);

    $this->delete(route('users.sessions.destroy-all', $this->targetUser->id))
        ->assertStatus(403);
});

test('super admin cannot manage their own sessions (policy prevention)', function () {
    $this->actingAs($this->superAdmin);

    // Cannot view own sessions
    $this->get(route('users.sessions.index', $this->superAdmin->id))
        ->assertStatus(403);

    // Cannot revoke own session
    $this->delete(route('users.sessions.destroy', [$this->superAdmin->id, 'session-id']))
        ->assertStatus(403);

    // Cannot revoke all own sessions
    $this->delete(route('users.sessions.destroy-all', $this->superAdmin->id))
        ->assertStatus(403);
});

test('super admin can view other users session list with parsed browser user agents', function (string $userAgent, string $expectedBrowser, string $expectedOs, string $expectedDevice) {
    // Seed session data for the target user
    DB::table('sessions')->insert([
        'id' => 'session-id-123',
        'user_id' => $this->targetUser->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => $userAgent,
        'payload' => 'dummy-payload',
        'last_activity' => now()->subMinutes(5)->timestamp,
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('users.sessions.index', $this->targetUser->id));

    $response->assertStatus(200);
    $response->assertViewIs('super_admin.users.sessions');
    $response->assertViewHas('user');
    $response->assertViewHas('sessions');

    $sessions = $response->viewData('sessions');
    expect($sessions)->toHaveCount(1);

    $parsedInfo = $sessions[0]->browser_info;
    expect($parsedInfo['browser'])->toBe($expectedBrowser);
    expect($parsedInfo['os'])->toBe($expectedOs);
    expect($parsedInfo['device'])->toBe($expectedDevice);
})->with([
    // [UserAgent, ExpectedBrowser, ExpectedOS, ExpectedDevice]
    ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0', 'Microsoft Edge', 'Windows', 'Desktop'],
    ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0', 'Opera', 'Windows', 'Desktop'],
    ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'Google Chrome', 'Windows', 'Desktop'],
    ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/121.0', 'Mozilla Firefox', 'macOS', 'Desktop'],
    ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15', 'Safari', 'macOS', 'Desktop'],
    ['Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36', 'Google Chrome', 'Android', 'Mobile'],
    ['Mozilla/5.0 (iPad; CPU OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/605.1.15', 'Safari', 'iOS', 'Tablet'],
    ['curl/8.4.0', 'cURL (API/Bot)', 'OS tidak diketahui', 'Desktop'],
    ['Unknown User Agent String', 'Browser tidak diketahui', 'OS tidak diketahui', 'Desktop'],
]);

test('super admin can revoke a specific session of another user', function () {
    // Seed target user's session
    DB::table('sessions')->insert([
        'id' => 'specific-session-id',
        'user_id' => $this->targetUser->id,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0',
        'payload' => 'dummy',
        'last_activity' => now()->timestamp,
    ]);

    // Assert session exists before deletion
    $this->assertDatabaseHas('sessions', [
        'id' => 'specific-session-id',
        'user_id' => $this->targetUser->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('users.sessions.destroy', [$this->targetUser->id, 'specific-session-id']));

    $response->assertRedirect(route('users.sessions.index', $this->targetUser->id));
    $response->assertSessionHas('success', 'Sesi berhasil dihentikan paksa.');

    // Assert session is deleted from database
    $this->assertDatabaseMissing('sessions', [
        'id' => 'specific-session-id',
    ]);

    // Assert audit trail logged for session revocation
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'subject_id' => $this->targetUser->id,
        'subject_type' => User::class,
        'event' => 'session_revoked',
        'description' => "Satu sesi aktif '{$this->targetUser->name}' ({$this->targetUser->email}) berhasil dihentikan paksa.",
    ]);
});

test('super admin can revoke all sessions of another user', function () {
    // Seed multiple sessions for target user
    DB::table('sessions')->insert([
        [
            'id' => 'session-1',
            'user_id' => $this->targetUser->id,
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0',
            'payload' => 'payload-1',
            'last_activity' => now()->timestamp,
        ],
        [
            'id' => 'session-2',
            'user_id' => $this->targetUser->id,
            'ip_address' => '192.168.1.2',
            'user_agent' => 'Mozilla/5.0',
            'payload' => 'payload-2',
            'last_activity' => now()->timestamp,
        ],
    ]);

    // Seed session for super admin (which should NOT be deleted)
    DB::table('sessions')->insert([
        'id' => 'super-admin-session',
        'user_id' => $this->superAdmin->id,
        'ip_address' => '10.0.0.1',
        'user_agent' => 'Mozilla/5.0',
        'payload' => 'super-admin-payload',
        'last_activity' => now()->timestamp,
    ]);

    // Assert sessions exist
    $this->assertDatabaseHas('sessions', ['id' => 'session-1']);
    $this->assertDatabaseHas('sessions', ['id' => 'session-2']);
    $this->assertDatabaseHas('sessions', ['id' => 'super-admin-session']);

    $response = $this->actingAs($this->superAdmin)->delete(route('users.sessions.destroy-all', $this->targetUser->id));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success', "Semua sesi aktif {$this->targetUser->name} berhasil dihentikan paksa.");

    // Target sessions should be deleted
    $this->assertDatabaseMissing('sessions', ['id' => 'session-1']);
    $this->assertDatabaseMissing('sessions', ['id' => 'session-2']);

    // Super admin session should remain untouched
    $this->assertDatabaseHas('sessions', ['id' => 'super-admin-session']);

    // Assert audit trail logged for revoking all sessions
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'subject_id' => $this->targetUser->id,
        'subject_type' => User::class,
        'event' => 'session_revoked',
        'description' => "Semua sesi aktif '{$this->targetUser->name}' ({$this->targetUser->email}) berhasil dihentikan paksa.",
    ]);
});
