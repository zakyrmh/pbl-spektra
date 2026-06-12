<?php

declare(strict_types=1);

use App\Enums\QueueStatus;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login when trying to access papan panggil page', function () {
    $response = $this->get(route('admin.papan-panggil'));
    $response->assertRedirect(route('login'));
});

test('non-admin_gerai user cannot access papan panggil page', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $response = $this->actingAs($user)->get(route('admin.papan-panggil'));
    $response->assertStatus(403);
});

test('admin_gerai without department receives 403 on papan panggil page', function () {
    $user = User::factory()->create([
        'role' => 'admin_gerai',
        'department_id' => null,
    ]);

    $response = $this->actingAs($user)->get(route('admin.papan-panggil'));
    $response->assertStatus(403);
});

test('admin_gerai with department can view the papan panggil page', function () {
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

    // Create a checked-in queue
    Queue::create([
        'user_id' => $citizen->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-XYZ123',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-001',
        'status' => QueueStatus::CheckedIn->value,
    ]);

    $response = $this->actingAs($user)->get(route('admin.papan-panggil'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.papan-panggil');
    $response->assertViewHas('department');
    $response->assertViewHas('activeBooking', null);
});

test('operator can call next checked-in queue', function () {
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

    $citizen1 = User::factory()->create(['role' => 'pengunjung']);
    $citizen2 = User::factory()->create(['role' => 'pengunjung']);

    // Queue 1: currently serving
    $queue1 = Queue::create([
        'user_id' => $citizen1->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-XYZ123',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-001',
        'status' => QueueStatus::Serving->value,
    ]);

    // Queue 2: checked-in waiting
    $queue2 = Queue::create([
        'user_id' => $citizen2->id,
        'department_id' => $dept->id,
        'booking_code' => 'WI-DK-2026-ABC456',
        'purpose' => 'Ambil KK',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DK-002',
        'status' => QueueStatus::CheckedIn->value,
    ]);

    $response = $this->actingAs($user)->post(route('admin.papan-panggil.next'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Queue 1 should now be completed
    $this->assertDatabaseHas('queues', [
        'id' => $queue1->id,
        'status' => QueueStatus::Completed->value,
    ]);

    // Queue 2 should now be serving
    $this->assertDatabaseHas('queues', [
        'id' => $queue2->id,
        'status' => QueueStatus::Serving->value,
    ]);
});

test('operator can complete active queue', function () {
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
        'status' => QueueStatus::Serving->value,
    ]);

    $response = $this->actingAs($user)->post(route('admin.papan-panggil.complete', $queue->id));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('queues', [
        'id' => $queue->id,
        'status' => QueueStatus::Completed->value,
    ]);
});

test('operator can skip active queue with reason', function () {
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
        'status' => QueueStatus::Serving->value,
    ]);

    // Validation fails if cancel_reason is missing
    $response = $this->actingAs($user)->post(route('admin.papan-panggil.skip', $queue->id), []);
    $response->assertSessionHasErrors(['cancel_reason']);

    // Successful skip
    $response = $this->actingAs($user)->post(route('admin.papan-panggil.skip', $queue->id), [
        'cancel_reason' => 'Warga tidak hadir setelah dipanggil 3 kali.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('queues', [
        'id' => $queue->id,
        'status' => QueueStatus::Cancelled->value,
        'cancel_reason' => 'Warga tidak hadir setelah dipanggil 3 kali.',
    ]);
});
