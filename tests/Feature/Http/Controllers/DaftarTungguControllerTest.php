<?php

declare(strict_types=1);

use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login when trying to access daftar tunggu page', function () {
    $response = $this->get(route('admin.daftar-tunggu'));
    $response->assertRedirect(route('login'));
});

test('non-admin_gerai user cannot access daftar tunggu page', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $response = $this->actingAs($user)->get(route('admin.daftar-tunggu'));
    $response->assertStatus(403);
});

test('admin_gerai without department receives 403 on daftar tunggu page', function () {
    $user = User::factory()->create([
        'role' => 'admin_gerai',
        'department_id' => null,
    ]);

    $response = $this->actingAs($user)->get(route('admin.daftar-tunggu'));
    $response->assertStatus(403);
});

test('admin_gerai with department can view the daftar tunggu page', function () {
    $dept = Department::create([
        'name' => 'Kependudukan',
        'inisial' => 'DK',
        'nomor_loket' => '02',
        'description' => 'Dinas Kependudukan',
        'is_open' => true,
    ]);

    $user = User::factory()->create([
        'role' => 'admin_gerai',
        'department_id' => $dept->id,
    ]);

    $citizen = User::factory()->create(['role' => 'pengunjung']);

    // Create a pending booking (Booked)
    Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-XYZ123',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-001',
        'status' => QueueStatus::Booked->value,
    ]);

    $response = $this->actingAs($user)->get(route('admin.daftar-tunggu'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.daftar-tunggu');
    $response->assertViewHas('department');
    $response->assertViewHas('pendingBookings');
    $response->assertViewHas('checkedInBookings');
    $response->assertViewHas('cancelledBookings');
});

test('operator can check-in a pending queue', function () {
    $dept = Department::create([
        'name' => 'Kependudukan',
        'inisial' => 'DK',
        'nomor_loket' => '02',
        'description' => 'Dinas Kependudukan',
        'is_open' => true,
    ]);

    $user = User::factory()->create([
        'role' => 'admin_gerai',
        'department_id' => $dept->id,
    ]);

    $citizen = User::factory()->create(['role' => 'pengunjung']);

    $queue = Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-XYZ123',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-001',
        'status' => QueueStatus::Booked->value,
    ]);

    $response = $this->actingAs($user)->post(route('admin.daftar-tunggu.check-in', $queue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('queues', [
        'id' => $queue->id,
        'status' => QueueStatus::CheckedIn->value,
    ]);
});

test('operator can restore a cancelled queue', function () {
    $dept = Department::create([
        'name' => 'Kependudukan',
        'inisial' => 'DK',
        'nomor_loket' => '02',
        'description' => 'Dinas Kependudukan',
        'is_open' => true,
    ]);

    $user = User::factory()->create([
        'role' => 'admin_gerai',
        'department_id' => $dept->id,
    ]);

    $citizen = User::factory()->create(['role' => 'pengunjung']);

    $queue = Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-XYZ123',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-001',
        'status' => QueueStatus::Cancelled->value,
        'cancel_reason' => 'Dilewati oleh operator',
        'checked_in_at' => now(),
    ]);

    $response = $this->actingAs($user)->post(route('admin.daftar-tunggu.restore', $queue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('queues', [
        'id' => $queue->id,
        'status' => QueueStatus::CheckedIn->value,
        'cancel_reason' => null,
    ]);
});
