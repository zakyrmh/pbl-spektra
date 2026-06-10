<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // 1. Create a Department for FO (critical since QueueCallController relies on: Department::where('inisial', 'FO')->first())
    $this->foDepartment = Department::create([
        'name' => 'Front Office',
        'inisial' => 'FO',
    ]);

    // 2. Create a Counter for FO (critical since QueueCallController relies on: Counter::where('department_id', $foDept->id)->first())
    $this->foCounter = Counter::create([
        'department_id' => $this->foDepartment->id,
        'name' => 'Loket FO',
        'status' => 'aktif',
    ]);

    // 3. Create a Service for FO
    $this->foService = Service::create([
        'department_id' => $this->foDepartment->id,
        'name' => 'Layanan Informasi',
    ]);

    // 4. Create an Admin FO user
    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // 5. Create a Visitor user
    $this->visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);
});

// ── Access Control ────────────────────────────────────────────────────────────

test('guest is redirected to login when accessing FO queue call routes', function () {
    $this->get(route('admin.fo.call'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.call.next'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.call.recall'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.call.skip'))->assertRedirect(route('login'));
});

test('visitor is forbidden (403) from accessing FO queue call routes', function () {
    $this->actingAs($this->visitor)->get(route('admin.fo.call'))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.call.next'))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.call.recall'))->assertStatus(403);
    $this->actingAs($this->visitor)->post(route('admin.fo.call.skip'))->assertStatus(403);
});

// ── GET Index ─────────────────────────────────────────────────────────────────

test('FO admin can view queue call dashboard with stats and queues', function () {
    $today = Carbon::today();

    // Create a serving queue today
    $servingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-001',
        'status' => 'Serving',
        'queue_date' => $today,
    ]);

    // Create a waiting queue today
    $waitingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-002',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    // Create completed and skipped queues today to test statistics
    Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-003',
        'status' => 'Completed',
        'queue_date' => $today,
    ]);

    Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-004',
        'status' => 'Skipped',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->foAdmin)->get(route('admin.fo.call'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.fo.call');
    $response->assertViewHas('currentQueue', function ($value) use ($servingQueue) {
        return $value->id === $servingQueue->id;
    });
    $response->assertViewHas('nextQueue', function ($value) use ($waitingQueue) {
        return $value->id === $waitingQueue->id;
    });
    $response->assertViewHas('totalWaiting', 1);
    $response->assertViewHas('totalServed', 1);
    $response->assertViewHas('totalSkipped', 1);
    $response->assertViewHas('counter', function ($value) {
        return $value->id === $this->foCounter->id;
    });
});

test('GET index returns 404 when FO department is missing', function () {
    // Delete the FO department
    $this->foDepartment->delete();

    $this->actingAs($this->foAdmin)->get(route('admin.fo.call'))->assertStatus(404);
});

test('GET index returns 404 when FO counter is missing', function () {
    // Delete the FO counter
    $this->foCounter->delete();

    $this->actingAs($this->foAdmin)->get(route('admin.fo.call'))->assertStatus(404);
});

// ── POST Next ─────────────────────────────────────────────────────────────────

test('next calls the waiting queue when no queue is currently serving', function () {
    $today = Carbon::today();

    $waitingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-001',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.next'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('success', 'Nomor antrean <strong>FO-001</strong> berhasil dipanggil.');
    $response->assertSessionHas('play_chime', true);

    // Assert status updated to Serving
    $waitingQueue->refresh();
    expect($waitingQueue->status)->toBe('Serving');
    expect($waitingQueue->called_at)->not->toBeNull();

    // Assert activity log recorded
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'CALL_NEXT_FO',
        'model_type' => 'Queue',
        'model_id' => $waitingQueue->id,
    ]);
});

test('next completes current serving queue and calls the next waiting queue', function () {
    $today = Carbon::today();

    $servingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-001',
        'status' => 'Serving',
        'queue_date' => $today,
    ]);

    $waitingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-002',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.next'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('success', 'Nomor antrean <strong>FO-002</strong> berhasil dipanggil.');
    $response->assertSessionHas('play_chime', true);

    // Assert serving queue completed
    $servingQueue->refresh();
    expect($servingQueue->status)->toBe('Completed');
    expect($servingQueue->completed_at)->not->toBeNull();

    // Assert waiting queue is now serving
    $waitingQueue->refresh();
    expect($waitingQueue->status)->toBe('Serving');
    expect($waitingQueue->called_at)->not->toBeNull();

    // Assert activity log recorded for the new serving queue
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'CALL_NEXT_FO',
        'model_type' => 'Queue',
        'model_id' => $waitingQueue->id,
    ]);
});

test('next returns warning when there are no waiting queues', function () {
    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.next'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('warning', 'Tidak ada antrean berikutnya yang sedang menunggu.');
});

// ── POST Recall ───────────────────────────────────────────────────────────────

test('recall updates called_at and returns success when there is a serving queue', function () {
    $today = Carbon::today();

    $servingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-001',
        'status' => 'Serving',
        'queue_date' => $today,
        'called_at' => now()->subMinutes(5),
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.recall'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('success', 'Memanggil ulang nomor antrean <strong>FO-001</strong>.');
    $response->assertSessionHas('play_chime', true);

    // Assert called_at updated
    $servingQueue->refresh();
    expect($servingQueue->called_at->gt(now()->subMinutes(1)))->toBeTrue();

    // Assert activity log recorded
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'RECALL_FO',
        'model_type' => 'Queue',
        'model_id' => $servingQueue->id,
    ]);
});

test('recall returns error when no queue is serving', function () {
    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.recall'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('error', 'Tidak ada antrean yang sedang aktif dilayani untuk dipanggil ulang.');
});

// ── POST Skip ─────────────────────────────────────────────────────────────────

test('skip updates status to Skipped and returns success when there is a serving queue', function () {
    $today = Carbon::today();

    $servingQueue = Queue::create([
        'counter_id' => $this->foCounter->id,
        'service_id' => $this->foService->id,
        'queue_number' => 'FO-001',
        'status' => 'Serving',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.skip'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('success', 'Nomor antrean <strong>FO-001</strong> dilewati.');

    // Assert status is Skipped and completed_at is set
    $servingQueue->refresh();
    expect($servingQueue->status)->toBe('Skipped');
    expect($servingQueue->completed_at)->not->toBeNull();

    // Assert activity log recorded
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'SKIP_FO',
        'model_type' => 'Queue',
        'model_id' => $servingQueue->id,
    ]);
});

test('skip returns error when no queue is serving', function () {
    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.call.skip'));

    $response->assertRedirect(route('admin.fo.call'));
    $response->assertSessionHas('error', 'Tidak ada antrean yang sedang aktif dilayani untuk dilewati.');
});
