<?php

use App\Events\QueueFinished;
use App\Mail\FeedbackRequestMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Schedule;
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
    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = test()->actingAs($operator)->get(route('antrean.index'));

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

    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $counter->department_id,
    ]);

    $response = test()->actingAs($operator)->get(route('antrean.index'));

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

    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($operatorNoCounter)->postJson(route('gerai.status'), [
        'status' => 'istirahat',
    ]);

    $response = test()->actingAs($operator)->post(route('gerai.status'), [
        'status' => 'istirahat',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'status' => 'istirahat',
    ]);

    test()->assertEquals('istirahat', $counter->fresh()->status);
});

test('operator can toggle their department operational status', function () {
    expect($this->dept->fresh()->is_open)->toBeTrue();

    $response = $this->actingAs($this->operator)->postJson(route('gerai.department.toggle'));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'is_open' => false,
    ]);

    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket 01',
        'status' => 'aktif',
    ]);

    $service = Service::create([
        'department_id' => $dept->id,
        'name' => 'Cetak KTP',
    ]);

    /** @var User $operator */
    $operator = User::factory()->create([
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

    // Panggil antrean berikutnya
    $response = test()->actingAs($operator)->post(route('gerai.call-next'));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    test()->assertEquals('Serving', $queue->fresh()->status);
    test()->assertNotNull($queue->fresh()->called_at);

    // Selesaikan antrean
    $response = test()->actingAs($operator)->post(route('gerai.finish', $queue->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    test()->assertEquals('Completed', $queue->fresh()->status);
    test()->assertNotNull($queue->fresh()->completed_at);
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

    $counterA = Counter::create(['department_id' => $deptA->id, 'name' => 'Loket DDK']);
    $counterB = Counter::create(['department_id' => $deptB->id, 'name' => 'Loket SMST']);

    /** @var User $operatorA */
    $operatorA = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $counterA->department_id,
    ]);

    $queueB = Queue::create([
        'counter_id' => $otherCounter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'SMST-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    // Coba panggil antrean milik loket B dengan operator A
    $response = test()->actingAs($operatorA)->post(route('gerai.call', $queueB->id));

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

// ── Papan Panggil Booking Tests ──────────────────────────────────────────────────

test('guest is redirected to login when accessing papan panggil', function () {
    $this->get(route('admin.papan-panggil'))->assertRedirect(route('login'));
});

test('operator with counter can access papan panggil page', function () {
    $response = $this->actingAs($this->operator)->get(route('admin.papan-panggil'));
    $response->assertStatus(200);
    $response->assertViewIs('admin.papan-panggil');
    $response->assertSee('Papan Panggil Instansi');
});

test('operator can call next booking successfully', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG12345',
        'status' => 'Pending',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Mengurus KTP Baru',
    ]);

    $response = $this->actingAs($this->operator)->post(route('admin.papan-panggil.next'));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Verify booking is checked in and stored in session
    $booking->refresh();
    expect($booking->status)->toBe('Checked-In');
    expect($booking->checked_in_at)->not->toBeNull();
    expect(session('papan_panggil_active_booking_id'))->toBe($booking->id);
});

test('operator next booking call returns error when no bookings left', function () {
    $response = $this->actingAs($this->operator)->post(route('admin.papan-panggil.next'));

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Tidak ada antrean tersisa untuk hari ini.');
});

test('operator can complete active booking', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG12345',
        'status' => 'Checked-In',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Mengurus KTP Baru',
        'checked_in_at' => now(),
    ]);

    session(['papan_panggil_active_booking_id' => $booking->id]);

    $response = $this->actingAs($this->operator)->post(route('admin.papan-panggil.complete', $booking));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('Completed');
    expect(session('papan_panggil_active_booking_id'))->toBeNull();
});

test('operator can skip active booking with reason', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG12345',
        'status' => 'Checked-In',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Mengurus KTP Baru',
        'checked_in_at' => now(),
    ]);

    session(['papan_panggil_active_booking_id' => $booking->id]);

    $response = $this->actingAs($this->operator)->post(route('admin.papan-panggil.skip', $booking), [
        'cancel_reason' => 'Pengunjung tidak hadir setelah dipanggil 3 kali',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('Cancelled');
    expect($booking->cancel_reason)->toBe('Pengunjung tidak hadir setelah dipanggil 3 kali');
    expect(session('papan_panggil_active_booking_id'))->toBeNull();
});

test('operator cannot skip active booking without min 5 characters reason', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 0,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG12345',
        'status' => 'Checked-In',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Mengurus KTP Baru',
        'checked_in_at' => now(),
    ]);

    session(['papan_panggil_active_booking_id' => $booking->id]);

    $response = $this->actingAs($this->operator)->post(route('admin.papan-panggil.skip', $booking), [
        'cancel_reason' => 'Btl', // too short
    ]);

    $response->assertSessionHasErrors(['cancel_reason']);
    $booking->refresh();
    expect($booking->status)->toBe('Checked-In'); // unchanged
});

// ── Daftar Tunggu Gerai ─────────────────────────────────────────────────────────

test('guest is redirected to login when accessing daftar tunggu', function () {
    $response = $this->get(route('admin.daftar-tunggu'));
    $response->assertRedirect(route('login'));
});

test('operator with counter can access daftar tunggu page and see list', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 2,
        'is_open' => true,
    ]);

    $bookingPending = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-PEND',
        'status' => 'Pending',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan A',
    ]);

    $bookingChecked = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-CHKD',
        'status' => 'Checked-In',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan B',
        'checked_in_at' => now(),
    ]);

    $bookingCancelled = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-CNCL',
        'status' => 'Cancelled',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan C',
        'cancel_reason' => 'Tidak hadir',
    ]);

    $response = $this->actingAs($this->operator)->get(route('admin.daftar-tunggu'));
    $response->assertStatus(200);
    $response->assertSee('BKG-PEND');
    $response->assertSee('BKG-CHKD');
    $response->assertSee('BKG-CNCL');
    $response->assertSee('Pagi');
});

test('operator can filter daftar tunggu by service and search by booking code/name', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 2,
        'is_open' => true,
    ]);

    $booking1 = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-SEARCH1',
        'status' => 'Pending',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan A',
    ]);

    $booking2 = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-SEARCH2',
        'status' => 'Pending',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan B',
    ]);

    // Search by code
    $response = $this->actingAs($this->operator)->get(route('admin.daftar-tunggu', ['search' => 'SEARCH1']));
    $response->assertSee('BKG-SEARCH1');
    $response->assertDontSee('BKG-SEARCH2');

    // Search by user name
    $response = $this->actingAs($this->operator)->get(route('admin.daftar-tunggu', ['search' => $this->visitor->name]));
    $response->assertSee('BKG-SEARCH1');
    $response->assertSee('BKG-SEARCH2');
});

test('operator can check-in booking manual successfully', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 2,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-MANUAL',
        'status' => 'Pending',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan A',
    ]);

    $response = $this->actingAs($this->operator)->post(route('admin.daftar-tunggu.check-in', $booking));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('Checked-In');
    expect($booking->checked_in_at)->not->toBeNull();

    // Verify Queue table entry
    $this->assertDatabaseHas('queues', [
        'booking_id' => $booking->id,
        'service_id' => $booking->service_id,
        'counter_id' => $this->counter->id,
        'status' => 'Waiting',
    ]);
});

test('operator cannot check-in non-pending booking', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 2,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-DOUBLE',
        'status' => 'Checked-In',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan A',
        'checked_in_at' => now(),
    ]);

    $response = $this->actingAs($this->operator)->post(route('admin.daftar-tunggu.check-in', $booking));
    $response->assertRedirect();
    $response->assertSessionHas('error');
});

test('operator can restore cancelled booking successfully', function () {
    $schedule = Schedule::create([
        'service_id' => $this->service->id,
        'date' => now()->toDateString(),
        'session_name' => 'Pagi',
        'quota_total' => 10,
        'quota_used' => 2,
        'is_open' => true,
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'schedule_id' => $schedule->id,
        'booking_code' => 'BKG-RESTORE',
        'status' => 'Cancelled',
        'booking_date' => now()->toDateString(),
        'purpose' => 'Layanan A',
        'cancel_reason' => 'Sengaja dibatalkan',
    ]);

    $response = $this->actingAs($this->operator)->post(route('admin.daftar-tunggu.restore', $booking));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $booking->refresh();
    expect($booking->status)->toBe('Pending');
    expect($booking->cancel_reason)->toBeNull();
});
