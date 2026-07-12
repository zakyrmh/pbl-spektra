<?php

use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $this->adminFo = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $this->department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
    ]);
});

test('admin fo can access fo dashboard successfully', function () {
    // Create some queues to populate FO dashboard
    Queue::create([
        'user_id' => $this->adminFo->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => QueueStatus::Booked->value,
    ]);

    $response = $this->actingAs($this->adminFo)->get(route('admin_fo.dashboard'));
    $response->assertStatus(200);
});

test('super admin can access superadmin dashboard successfully', function () {
    // Create some queues to populate SuperAdmin dashboard
    Queue::create([
        'user_id' => $this->superAdmin->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-67890',
        'purpose' => 'Layanan Umum',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-002',
        'status' => QueueStatus::CheckedIn->value,
        'checked_in_at' => now(),
    ]);

    $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
    $response->assertStatus(200);
});

test('cancel expired bookings command runs successfully', function () {
    // Create a pending booking that is expired (date in the past)
    $expiredQueue = Queue::create([
        'user_id' => $this->superAdmin->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-EXPIRED',
        'purpose' => 'Layanan Umum',
        'session_name' => 'Pagi',
        'booking_date' => now()->subDays(2)->toDateString(),
        'status' => QueueStatus::Booked->value,
    ]);

    // Run command
    $this->artisan('bookings:cancel-expired')
        ->assertExitCode(0);

    // Assert that the expired queue is now Cancelled
    $expiredQueue->refresh();
    expect($expiredQueue->status)->toBe(QueueStatus::Cancelled);
});
