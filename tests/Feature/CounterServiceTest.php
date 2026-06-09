<?php

use App\Events\QueueCalled;
use App\Events\QueueFinished;
use App\Mail\FeedbackRequestMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed basic department, counter, and service
    $this->dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
    ]);

    $this->counter = Counter::create([
        'department_id' => $this->dept->id,
        'name' => 'Loket 01',
        'status' => 'aktif',
    ]);

    $this->service = Service::create([
        'department_id' => $this->dept->id,
        'name' => 'Cetak KTP',
    ]);

    // Create an operator user assigned to the department (which resolves counter dynamically)
    $this->operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $this->dept->id,
    ]);

    // Create a generic visitor/customer user
    $this->visitor = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);
});

// ── Dashboard View & Metrics ──────────────────────────────────────────────────

test('guest is redirected to login when accessing operator dashboard', function () {
    $this->get(route('antrean.index'))->assertRedirect(route('login'));
});

test('operator with no counter is shown warning page', function () {
    $operatorNoCounter = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($operatorNoCounter)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.dashboard');
    $response->assertViewHas('noCounter', true);
    $response->assertSee('Loket Belum Ditugaskan');
});

test('operator with departments_id set but no counter in database is shown warning page', function () {
    $deptNoCounter = Department::create([
        'name' => 'Gerai Tanpa Loket',
        'inisial' => 'GTL',
    ]);

    $operatorNoCounter = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $deptNoCounter->id,
    ]);

    $response = $this->actingAs($operatorNoCounter)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.dashboard');
    $response->assertViewHas('noCounter', true);
});

test('operator with counter can view the dashboard and see their counter info', function () {
    $response = $this->actingAs($this->operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.dashboard');
    $response->assertViewHas('counter', function ($c) {
        return $c->id === $this->counter->id;
    });
    $response->assertSee('Loket Loket 01');
    $response->assertSee('Disdukcapil');
});

test('operator dashboard correctly calculates avgServiceTime with no completed queues today', function () {
    $response = $this->actingAs($this->operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewHas('avgServiceTime', 12); // defaults to 12 minutes
});

test('operator dashboard correctly calculates avgServiceTime with completed queues today', function () {
    $today = Carbon::today();

    // Completed Queue 1: duration 10 mins (600 seconds)
    Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => $today,
        'called_at' => now()->subMinutes(15),
        'completed_at' => now()->subMinutes(5),
    ]);

    // Completed Queue 2: duration 20 mins (1200 seconds)
    Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-002',
        'status' => 'Completed',
        'queue_date' => $today,
        'called_at' => now()->subMinutes(30),
        'completed_at' => now()->subMinutes(10),
    ]);

    // Average duration = (600 + 1200) / 2 = 900 seconds = 15 minutes.
    $response = $this->actingAs($this->operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewHas('avgServiceTime', 15);
});

test('operator dashboard rounds up avgServiceTime to 1 if it is less than 1 minute', function () {
    $today = Carbon::today();

    // Completed Queue: duration 15 seconds
    Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => $today,
        'called_at' => now()->subSeconds(15),
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertViewHas('avgServiceTime', 1);
});

// ── Update Status ─────────────────────────────────────────────────────────────

test('operator without counter cannot update counter status', function () {
    $operatorNoCounter = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($operatorNoCounter)->postJson(route('gerai.status'), [
        'status' => 'istirahat',
    ]);

    $response->assertStatus(403);
    $response->assertJsonPath('message', 'Anda belum ditugaskan ke loket mana pun.');
});

test('operator can update their counter status with valid status', function () {
    $response = $this->actingAs($this->operator)->postJson(route('gerai.status'), [
        'status' => 'istirahat',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'status' => 'istirahat',
    ]);

    expect($this->counter->fresh()->status)->toBe('istirahat');

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->operator->id,
        'action' => 'UPDATE_COUNTER_STATUS',
        'model_type' => 'Counter',
        'model_id' => $this->counter->id,
    ]);
});

test('operator status update validation fails on invalid status', function () {
    $response = $this->actingAs($this->operator)->postJson(route('gerai.status'), [
        'status' => 'tidur', // invalid status
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['status']);
});

// ── Call Next ─────────────────────────────────────────────────────────────────

test('operator without counter cannot call next', function () {
    $operatorNoCounter = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($operatorNoCounter)->postJson(route('gerai.call-next'));

    $response->assertStatus(403);
});

test('operator cannot call next when there are no waiting queues', function () {
    $response = $this->actingAs($this->operator)->postJson(route('gerai.call-next'));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => false,
        'message' => 'Tidak ada antrean berikutnya yang sedang menunggu.',
    ]);
});

test('operator can call next queue successfully', function () {
    Event::fake();

    $queue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.call-next'));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('queue.id', $queue->id);
    $response->assertJsonPath('queue.status', 'Serving');

    $queue->refresh();
    expect($queue->status)->toBe('Serving');
    expect($queue->called_at)->not->toBeNull();

    // Assert event was broadcast
    Event::assertDispatched(QueueCalled::class, function ($event) use ($queue) {
        return $event->queue->id === $queue->id;
    });

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->operator->id,
        'action' => 'CALL_QUEUE',
        'model_type' => 'Queue',
        'model_id' => $queue->id,
    ]);
});

test('operator calling next automatically completes another serving queue at the same counter', function () {
    $today = now()->toDateString();

    $oldServing = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Serving',
        'queue_date' => $today,
        'called_at' => now()->subMinutes(10),
    ]);

    $nextQueue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-002',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.call-next'));

    $response->assertStatus(200);

    // Old serving queue must be Completed
    $oldServing->refresh();
    expect($oldServing->status)->toBe('Completed');
    expect($oldServing->completed_at)->not->toBeNull();

    // Next queue must be Serving
    $nextQueue->refresh();
    expect($nextQueue->status)->toBe('Serving');
    expect($nextQueue->called_at)->not->toBeNull();
});

// ── Call Queue (Direct ID) ───────────────────────────────────────────────────

test('operator can call target queue directly', function () {
    Event::fake();

    $queue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-005',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.call', $queue->id));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('queue.status', 'Serving');

    $queue->refresh();
    expect($queue->status)->toBe('Serving');

    Event::assertDispatched(QueueCalled::class);
});

test('operator cannot call target queue directly if unauthorized (different department)', function () {
    $otherDept = Department::create(['name' => 'Samsat', 'inisial' => 'SMST']);
    $otherCounter = Counter::create(['department_id' => $otherDept->id, 'name' => 'Loket SMST']);

    $queueB = Queue::create([
        'counter_id' => $otherCounter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'SMST-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.call', $queueB->id));

    $response->assertStatus(403);
});

// ── Finish Service ────────────────────────────────────────────────────────────

test('completing a service validates that queue status is Serving', function () {
    $queue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting', // not Serving
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.finish', $queue->id));

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Hanya antrean berstatus Serving yang dapat diselesaikan.',
    ]);
});

test('completing a service updates queue, records log, sends notification and feedback email for online booking', function () {
    Mail::fake();
    Event::fake();

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Checked-In',
        'booking_code' => 'B-ONLINE-123',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Serving',
        'queue_date' => now()->toDateString(),
        'called_at' => now()->subMinutes(10),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.finish', $queue->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Status antrean berhasil diperbarui.',
    ]);

    $queue->refresh();
    expect($queue->status)->toBe('Completed');
    expect($queue->completed_at)->not->toBeNull();

    // Assert notification created for online customer
    $this->assertDatabaseHas('notifications', [
        'user_id' => $this->visitor->id,
        'title' => 'Pelayanan Selesai',
        'message' => 'Pelayanan untuk nomor antrean DDK-001 telah selesai. Silakan isi ulasan dan berikan feedback Anda di menu Dashboard.',
    ]);

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->operator->id,
        'action' => 'COMPLETE_QUEUE',
        'model_type' => 'Queue',
        'model_id' => $queue->id,
    ]);

    // Assert Event triggered
    Event::assertDispatched(QueueFinished::class, function ($event) use ($queue) {
        return $event->queue->id === $queue->id;
    });

    // Assert Feedback Request Mail Sent
    Mail::assertSent(FeedbackRequestMail::class, function ($mail) use ($queue) {
        return $mail->hasTo($this->visitor->email) && $mail->queueModel->id === $queue->id;
    });
});

// ── Skip Service ──────────────────────────────────────────────────────────────

test('skipping a service validates that queue status is Serving', function () {
    $queue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting', // not Serving
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.skip', $queue->id));

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Hanya antrean aktif yang sedang dilayani yang dapat dilewati.',
    ]);
});

test('skipping a service updates queue status to Skipped and records log', function () {
    Event::fake();

    $queue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Serving',
        'queue_date' => now()->toDateString(),
        'called_at' => now()->subMinutes(5),
    ]);

    $response = $this->actingAs($this->operator)->postJson(route('gerai.skip', $queue->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Antrean berhasil dilewati.',
    ]);

    $queue->refresh();
    expect($queue->status)->toBe('Skipped');
    expect($queue->completed_at)->not->toBeNull();

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->operator->id,
        'action' => 'SKIP_QUEUE',
        'model_type' => 'Queue',
        'model_id' => $queue->id,
    ]);

    // Assert Event triggered
    Event::assertDispatched(QueueFinished::class);
});
