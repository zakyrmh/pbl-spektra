<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Basic setup
    $this->superAdmin = User::factory()->create(['role' => 'super_admin']);
    $this->foAdmin = User::factory()->create(['role' => 'admin_fo']);
    $this->operator = User::factory()->create(['role' => 'admin_gerai']);
    $this->visitor = User::factory()->create(['role' => 'pengunjung']);

    $this->dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
    ]);

    $this->counter = Counter::create([
        'department_id' => $this->dept->id,
        'name' => 'Loket 1',
        'status' => 'aktif',
    ]);

    $this->service = Service::create([
        'department_id' => $this->dept->id,
        'name' => 'Cetak KTP',
    ]);
});

// ── Access & Redirection ──────────────────────────────────────────────────────

test('guest is redirected to login from dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('operator admin_gerai is redirected to antrean.index from dashboard', function () {
    $this->actingAs($this->operator)->get(route('dashboard'))
        ->assertRedirect(route('antrean.index'));
});

// ── Super Admin Dashboard ─────────────────────────────────────────────────────

test('super admin dashboard calculates stats and percentages correctly (empty state)', function () {
    $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewIs('dashboard.dashboard');

    $response->assertViewHas('todayKunjunganCount', 0);
    $response->assertViewHas('kunjunganPercentage.value', 0.0);
    $response->assertViewHas('kunjunganPercentage.formatted', '+0%');

    $response->assertViewHas('menungguFoCount', 0);
    $response->assertViewHas('foStatus.label', 'Lancar');

    $response->assertViewHas('waitingCount', 0);
    $response->assertViewHas('servingCount', 0);
    $response->assertViewHas('totalAntreanGerai', 0);

    $response->assertViewHas('totalGerai', 1);
    $response->assertViewHas('activeGerai', 1);
    $response->assertViewHas('geraiPercentage', 100);

    $response->assertViewHas('avgFoCheckInTime', null);
});

test('super admin dashboard gets status Lancar, Sedang, or Padat based on pending bookings count', function (int $pendingCount, string $expectedLabel) {
    // Create pending bookings today
    for ($i = 0; $i < $pendingCount; $i++) {
        Booking::create([
            'user_id' => $this->visitor->id,
            'service_id' => $this->service->id,
            'booking_date' => now()->toDateString(),
            'status' => 'Pending',
            'booking_code' => "B-PEND-$i",
        ]);
    }

    $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('foStatus.label', $expectedLabel);
})->with([
    [3, 'Lancar'],
    [8, 'Sedang'],
    [16, 'Padat'],
]);

test('super admin dashboard calculates kunjunganPercentage and avgFoCheckInTime correctly', function () {
    $today = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();

    // Past 30 days daily stats: 10 visits yesterday (average is 10)
    for ($i = 0; $i < 10; $i++) {
        Queue::create([
            'counter_id' => $this->counter->id,
            'service_id' => $this->service->id,
            'queue_number' => "DDK-OLD-$i",
            'status' => 'Completed',
            'queue_date' => $yesterday,
        ]);
    }

    // Today's stats: 5 visits today (difference is -50%)
    for ($i = 0; $i < 5; $i++) {
        Queue::create([
            'counter_id' => $this->counter->id,
            'service_id' => $this->service->id,
            'queue_number' => "DDK-TODAY-$i",
            'status' => 'Waiting',
            'queue_date' => $today,
        ]);
    }

    // Today's checked-in bookings: 2 bookings (so checkedInBookingsCount is 2)
    for ($i = 0; $i < 2; $i++) {
        Booking::create([
            'user_id' => $this->visitor->id,
            'service_id' => $this->service->id,
            'booking_date' => $today,
            'status' => 'Checked-In',
            'booking_code' => "B-CI-$i",
            'checked_in_at' => now(),
        ]);
    }

    $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));
    $response->assertViewHas('kunjunganPercentage.value', -50.0);
    $response->assertViewHas('kunjunganPercentage.formatted', '-50%');
    $response->assertViewHas('kunjunganPercentage.is_increase', false);

    // avgFoCheckInTime = 1.2 + (2 % 5) * 0.3 = 1.2 + 0.6 = 1.8
    $response->assertViewHas('avgFoCheckInTime', function ($value) {
        return abs($value - 1.8) < 0.0001;
    });
});

test('super admin dashboard returns live feed logs and top gerai data sorted descending', function () {
    // Create another department and counter with more queues
    $otherDept = Department::create(['name' => 'Samsat', 'inisial' => 'SMST']);
    $otherCounter = Counter::create(['department_id' => $otherDept->id, 'name' => 'Loket Samsat', 'status' => 'aktif']);

    // Seed queues to check top gerai sorting: Samsat has 2 queues, Disdukcapil has 1 queue
    Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    Queue::create([
        'counter_id' => $otherCounter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'SMST-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    Queue::create([
        'counter_id' => $otherCounter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'SMST-002',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    // Record activity logs
    ActivityLog::record('TEST_ACTION', 'User', $this->visitor->id, 'Testing dashboard log');

    $response = $this->actingAs($this->superAdmin)->get(route('dashboard'));

    $response->assertStatus(200);

    // Verify logs
    $response->assertViewHas('liveLogs', function ($logs) {
        return $logs->count() === 1 && $logs->first()->description === 'Testing dashboard log';
    });

    // Verify top gerai data sorting: Samsat (2) must be first, Disdukcapil (1) second
    $response->assertViewHas('chartTopGeraiData', function ($chartData) {
        return $chartData['keys'][0] === 'Samsat' && $chartData['values'][0] === 2 &&
               $chartData['keys'][1] === 'Disdukcapil' && $chartData['values'][1] === 1;
    });
});

// ── Admin FO Dashboard ────────────────────────────────────────────────────────

test('admin_fo dashboard retrieves FO stats and recent printed tickets', function () {
    $today = now()->toDateString();

    // Create 1 pending booking today
    Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'booking_date' => $today,
        'status' => 'Pending',
        'booking_code' => 'B-FO-PEND',
    ]);

    // Create 1 printed ticket today
    Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-100',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->foAdmin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('todayFoQueueCount', 1);
    $response->assertViewHas('todayTotalPrintedTickets', 1);
    $response->assertViewHas('departments');
    $response->assertViewHas('recentQueues');
});

// ── Visitor (Pengunjung) Dashboard ───────────────────────────────────────────

test('visitor dashboard returns empty state when there are no active bookings', function () {
    $response = $this->actingAs($this->visitor)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('activeBooking', null);
    $response->assertViewHas('currentServingQueue', 'Belum Mulai');
    $response->assertViewHas('remainingQueuesCount', 0);
    $response->assertViewHas('estimatedTime', 0);
});

test('visitor dashboard calculates wait time metrics when visitor has active queue', function () {
    $today = now()->toDateString();

    // Create other queues at the same counter:
    // 1 Serving queue (FO-001)
    $servingQueue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'FO-001',
        'status' => 'Serving',
        'queue_date' => $today,
    ]);

    // 1 Waiting queue in front of user (FO-002)
    $frontQueue = Queue::create([
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'FO-002',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    // User's own queue (FO-003)
    $booking = Booking::create([
        'user_id' => $this->visitor->id,
        'service_id' => $this->service->id,
        'booking_date' => $today,
        'status' => 'Checked-In',
        'booking_code' => 'B-USER-1',
    ]);

    $userQueue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'FO-003',
        'status' => 'Waiting',
        'queue_date' => $today,
    ]);

    $response = $this->actingAs($this->visitor)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('activeBooking', function ($b) use ($booking) {
        return $b->id === $booking->id;
    });
    $response->assertViewHas('currentServingQueue', 'FO-001');
    $response->assertViewHas('remainingQueuesCount', 1); // only FO-002 is waiting in front of FO-003
    $response->assertViewHas('estimatedTime', 3); // 1 * 3 minutes
});
