<?php

declare(strict_types=1);

use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login when trying to access walk-in page', function () {
    $response = $this->get(route('admin.fo.ticket.create'));
    $response->assertRedirect(route('login'));
});

test('admin fo can view the walk-in ticket form', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $response = $this->actingAs($user)->get(route('admin.fo.ticket.create'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.fo.print-ticket');
    $response->assertViewHas('departments');
});

test('admin fo can issue a walk-in ticket with NIK for existing user', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $citizen = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
        'name' => 'Ahmad Hidayat',
        'no_telp' => '081234567890',
    ]);

    $response = $this->actingAs($user)->post(route('admin.fo.ticket.store'), [
        'department_id' => $department->id,
        'name' => 'Ahmad Hidayat',
        'nik' => '1234567890123456',
        'phone' => '081234567890',
        'purpose' => 'Pengurusan KTP Baru',
    ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('ticket');

    $this->assertDatabaseHas('queues', [
        'user_id' => $citizen->id,
        'department_id' => $department->id,
        'purpose' => 'Pengurusan KTP Baru',
        'queue_number' => 'LK-001',
        'status' => QueueStatus::CheckedIn->value,
    ]);
});

test('admin fo can issue a walk-in ticket for a new user (with or without NIK)', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    // Test ticket generation with null/empty NIK
    $response = $this->actingAs($user)->post(route('admin.fo.ticket.store'), [
        'department_id' => $department->id,
        'name' => 'WalkIn Guest',
        'nik' => '',
        'phone' => '089988776655',
        'purpose' => 'Pertanyaan Informasi Publik',
    ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHas('success');
    $createdQueue = session('ticket');
    $queueNumber = is_array($createdQueue) ? $createdQueue['queue_number'] : $createdQueue->queue_number;
    expect($queueNumber)->toBe('LK-001');

    $this->assertDatabaseHas('users', [
        'name' => 'WalkIn Guest',
        'nik' => null,
        'no_telp' => '089988776655',
    ]);
});

test('duplicate walk-in tickets for same NIK is blocked', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $citizen = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    // First issue
    $this->actingAs($user)->post(route('admin.fo.ticket.store'), [
        'department_id' => $department->id,
        'name' => 'Ahmad Hidayat',
        'nik' => '1234567890123456',
        'phone' => '081234567890',
        'purpose' => 'First issue',
    ]);

    // Second issue with same NIK should fail validation
    $response = $this->actingAs($user)->post(route('admin.fo.ticket.store'), [
        'department_id' => $department->id,
        'name' => 'Ahmad Hidayat',
        'nik' => '1234567890123456',
        'phone' => '081234567890',
        'purpose' => 'Second issue',
    ]);

    $response->assertSessionHasErrors(['nik']);
});

test('guest cannot check NIK', function () {
    $response = $this->getJson(route('api.fo.visitors.check-nik', ['nik' => '1234567890123456']));
    $response->assertStatus(401);
});

test('non-admin_fo user cannot check NIK', function () {
    $user = User::factory()->create([
        'role' => 'admin_gerai',
    ]);

    $response = $this->actingAs($user)->getJson(route('api.fo.visitors.check-nik', ['nik' => '1234567890123456']));
    $response->assertStatus(403);
});

test('admin fo checking non-existent NIK receives 404', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $response = $this->actingAs($user)->getJson(route('api.fo.visitors.check-nik', ['nik' => '9999999999999999']));

    $response->assertStatus(404);
    $response->assertJson([
        'is_found' => false,
    ]);
});

test('admin fo checking NIK of a visitor role user receives 200 with data', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
        'name' => 'Budi Santoso',
        'no_telp' => '081299990000',
    ]);

    $response = $this->actingAs($user)->getJson(route('api.fo.visitors.check-nik', ['nik' => '1234567890123456']));

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $visitor->id,
            'name' => 'Budi Santoso',
            'no_telp' => '081299990000',
            'is_found' => true,
        ],
    ]);
});

test('admin fo checking NIK of a non-visitor role user receives 404', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $staff = User::factory()->create([
        'role' => 'admin_gerai',
        'nik' => '1234567890123456',
    ]);

    $response = $this->actingAs($user)->getJson(route('api.fo.visitors.check-nik', ['nik' => '1234567890123456']));

    $response->assertStatus(404);
});
