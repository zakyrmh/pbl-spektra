<?php

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use App\Services\AdminGerai\BoothOperationService;
use App\Services\Public\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengunjung dapat melakukan booking mandiri dengan multi-gerai (waterfall queue)', function () {
    $user = User::factory()->create([
        'role' => UserRole::Pengunjung,
        'is_priority' => false,
    ]);

    $deptA = Department::create([
        'name' => 'Gerai A',
        'inisial' => 'GRA',
        'nomor_loket' => '1',
        'description' => 'Layanan A',
        'is_open' => true,
    ]);

    $deptB = Department::create([
        'name' => 'Gerai B',
        'inisial' => 'GRB',
        'nomor_loket' => '2',
        'description' => 'Layanan B',
        'is_open' => true,
    ]);

    $bookingService = app(BookingService::class);

    $data = [
        'department_id' => $deptA->id,
        'next_department_ids' => [$deptB->id],
        'keperluan' => 'Pengurusan KTP dan Perizinan',
        'booking_date' => now()->toDateString(),
        'session_name' => 'Sesi 1',
    ];

    $queue = $bookingService->processBookingCreation($user->id, $data);

    expect($queue)->not->toBeNull();
    expect($queue->department_id)->toBe($deptA->id);
    expect($queue->next_department_ids)->toBe([$deptB->id]);
    expect($queue->status->value ?? $queue->status)->toBe('Booked');
    expect($queue->sequence_order)->toBe(1);

    // Gerai B belum memiliki antrean aktif di database
    expect(Queue::where('department_id', $deptB->id)->count())->toBe(0);
});

test('petugas gerai A yang menekan selesai akan otomatis menerbitkan tiket gerai B dengan status Checked-In dan is_waterfall_forwarded = true', function () {
    $user = User::factory()->create([
        'role' => UserRole::Pengunjung,
        'is_priority' => false,
    ]);

    $deptA = Department::create([
        'name' => 'Gerai A',
        'inisial' => 'GRA',
        'nomor_loket' => '1',
        'description' => 'Layanan A',
        'is_open' => true,
    ]);

    $deptB = Department::create([
        'name' => 'Gerai B',
        'inisial' => 'GRB',
        'nomor_loket' => '2',
        'description' => 'Layanan B',
        'is_open' => true,
    ]);

    $operatorA = User::factory()->create([
        'role' => UserRole::AdminGerai,
        'departments_id' => $deptA->id,
    ]);

    // Tiket di Gerai A sedang Serving
    $queueA = Queue::create([
        'user_id' => $user->id,
        'department_id' => $deptA->id,
        'next_department_ids' => [$deptB->id],
        'booking_code' => 'BK-GRA-20260801-123456',
        'purpose' => 'Layanan Multi-Gerai',
        'session_name' => 'Sesi 1',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'GRA-001',
        'sequence_order' => 1,
        'status' => QueueStatus::Serving->value,
        'called_at' => now(),
        'checked_in_at' => now(),
    ]);

    $boothService = app(BoothOperationService::class);
    $nextQueue = $boothService->finishService($queueA, $operatorA, 'Selesai pelayanan Gerai A');

    // Gerai A sudah Completed
    $queueA->refresh();
    expect($queueA->status->value ?? $queueA->status)->toBe('Completed');

    // Antrean Gerai B terbit secara otomatis
    expect($nextQueue)->not->toBeNull();
    expect($nextQueue->department_id)->toBe($deptB->id);
    expect($nextQueue->status->value ?? $nextQueue->status)->toBe('Checked-In');
    expect($nextQueue->is_waterfall_forwarded)->toBeTrue();
    expect($nextQueue->is_priority)->toBeFalse(); // is_priority tetap false untuk non-disabilitas
    expect($nextQueue->sequence_order)->toBe(2);
    expect($nextQueue->parent_queue_id)->toBe($queueA->id);
});

test('antrean dengan is_waterfall_forwarded disisipkan di posisi terdepan antrean reguler di Gerai B', function () {
    $deptB = Department::create([
        'name' => 'Gerai B',
        'inisial' => 'GRB',
        'nomor_loket' => '2',
        'description' => 'Layanan B',
        'is_open' => true,
    ]);

    $operatorB = User::factory()->create([
        'role' => UserRole::AdminGerai,
        'departments_id' => $deptB->id,
    ]);

    $today = now()->toDateString();

    // Antrean reguler 1 (Checked-In lebih awal)
    $qReguler1 = Queue::create([
        'user_id' => User::factory()->create()->id,
        'department_id' => $deptB->id,
        'booking_code' => 'BK-GRB-1',
        'purpose' => 'Reguler 1',
        'session_name' => 'Sesi 1',
        'booking_date' => $today,
        'queue_number' => 'GRB-001',
        'status' => QueueStatus::CheckedIn->value,
        'is_priority' => false,
        'is_waterfall_forwarded' => false,
        'checked_in_at' => now()->subMinutes(10),
    ]);

    // Antrean waterfall terusan dari Gerai A (Checked-In belakangan tapi waterfall forwarded)
    $qWaterfall = Queue::create([
        'user_id' => User::factory()->create()->id,
        'department_id' => $deptB->id,
        'booking_code' => 'BK-GRB-WF',
        'purpose' => 'Waterfall Terusan',
        'session_name' => 'Sesi 1',
        'booking_date' => $today,
        'queue_number' => 'GRB-002',
        'status' => QueueStatus::CheckedIn->value,
        'is_priority' => false,
        'is_waterfall_forwarded' => true,
        'checked_in_at' => now()->subMinutes(2),
    ]);

    // Antrean disabilitas kelompok rentan (Priority)
    $qDisabilitas = Queue::create([
        'user_id' => User::factory()->create(['is_priority' => true])->id,
        'department_id' => $deptB->id,
        'booking_code' => 'BK-GRB-DIS',
        'purpose' => 'Layanan Disabilitas',
        'session_name' => 'Sesi 1',
        'booking_date' => $today,
        'queue_number' => 'P-001',
        'status' => QueueStatus::CheckedIn->value,
        'is_priority' => true,
        'is_waterfall_forwarded' => false,
        'checked_in_at' => now(),
    ]);

    $boothService = app(BoothOperationService::class);

    // Call 1: Harus memanggil antrean Disabilitas
    $called1 = $boothService->callNext($operatorB);
    expect($called1->id)->toBe($qDisabilitas->id);

    // Complete call 1
    $boothService->finishService($called1, $operatorB);

    // Call 2: Harus memanggil antrean Waterfall Forwarded (sebelum antrean reguler)
    $called2 = $boothService->callNext($operatorB);
    expect($called2->id)->toBe($qWaterfall->id);
});
