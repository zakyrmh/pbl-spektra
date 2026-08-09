<?php

use App\Enums\QueueStatus;
use App\Exports\QueuesExport;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

test('admin fo can view draft report detail', function () {
    $adminFo = User::factory()->create([
        'role' => 'admin_fo',
        'email_verified_at' => now(),
    ]);

    $report = Report::create([
        'created_by' => $adminFo->id,
        'title' => 'Laporan Kinerja Mei 2026',
        'start_date' => now()->subDays(7)->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => [
            'total_visitors' => 10,
            'completed_count' => 8,
            'skipped_count' => 2,
            'attendance_rate' => 80.0,
            'avg_service_time' => 5.5,
            'avg_waiting_time' => 3.2,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->actingAs($adminFo)->get(route('admin.fo.reports.show', $report));

    $response->assertStatus(200);
    $response->assertSee('Laporan Kinerja Mei 2026');
    $response->assertSee('Draft / Belum Dikirim');
});

test('admin fo can view sent report detail', function () {
    $adminFo = User::factory()->create([
        'role' => 'admin_fo',
        'email_verified_at' => now(),
    ]);

    $report = Report::create([
        'created_by' => $adminFo->id,
        'title' => 'Laporan Kinerja April 2026',
        'start_date' => now()->subDays(30)->toDateString(),
        'end_date' => now()->subDays(1)->toDateString(),
        'data_summary' => [
            'total_visitors' => 25,
            'completed_count' => 20,
            'skipped_count' => 5,
            'attendance_rate' => 80.0,
            'avg_service_time' => 6.0,
            'avg_waiting_time' => 4.0,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Terkirim',
    ]);

    $response = $this->actingAs($adminFo)->get(route('admin.fo.reports.show', $report));

    $response->assertStatus(200);
    $response->assertSee('Laporan Kinerja April 2026');
    $response->assertSee('Terkirim ke Super Admin');
});

test('guest cannot view fo report detail', function () {
    $user = User::factory()->create();

    $report = Report::create([
        'created_by' => $user->id,
        'title' => 'Laporan Rahasia',
        'start_date' => now()->subDays(7)->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => [],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->get(route('admin.fo.reports.show', $report));

    $response->assertRedirect(route('login'));
});

test('super admin can download report excel directly without notification or job', function () {
    Bus::fake();

    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'no_telp' => '081234567890',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
    ]);

    $startDate = now()->subDays(3)->toDateString();
    $endDate = now()->toDateString();

    Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-EXPORT-1',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->subDay()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => QueueStatus::Completed->value,
        'called_at' => now()->subDay()->setTime(9, 0),
        'completed_at' => now()->subDay()->setTime(9, 15),
    ]);

    $report = Report::create([
        'created_by' => $superAdmin->id,
        'title' => 'Laporan Kinerja Export Test',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'data_summary' => [
            'total_visitors' => 1,
            'completed_count' => 1,
            'skipped_count' => 0,
            'attendance_rate' => 100.0,
            'avg_service_time' => 15.0,
            'avg_waiting_time' => 0.0,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Terkirim',
    ]);

    $filename = 'rekap-kunjungan-mpp-'.$startDate.'-to-'.$endDate.'.xlsx';

    $response = $this->actingAs($superAdmin)->get(route('admin.reports.export.excel', $report));

    $response->assertSuccessful();
    $response->assertDownload($filename);

    expect(Notification::query()->count())->toBe(0);
    Bus::assertNothingDispatched();
});

test('queues export includes visitor phone number column', function () {
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'no_telp' => '081298765432',
        'nik' => '1371010101010001',
    ]);

    $department = Department::create([
        'name' => 'Dinas Sosial',
        'inisial' => 'DS',
        'nomor_loket' => '02',
    ]);

    $queue = Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-EXPORT-2',
        'purpose' => 'SKTM',
        'session_name' => 'Siang',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'DS-001',
        'status' => QueueStatus::Completed->value,
        'called_at' => now()->setTime(13, 0),
        'completed_at' => now()->setTime(13, 20),
    ]);

    $queue->load(['user', 'department']);

    $export = new QueuesExport(now()->toDateString(), now()->toDateString());

    expect($export->headings())->toContain('Nomor HP Pengunjung')
        ->and($export->map($queue))->toContain('081298765432');
});

test('fo report detail table shows visitor phone number', function () {
    $adminFo = User::factory()->create([
        'role' => 'admin_fo',
        'email_verified_at' => now(),
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'name' => 'Budi Santoso',
        'no_telp' => '081355512345',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
    ]);

    $startDate = now()->subDays(2)->toDateString();
    $endDate = now()->toDateString();

    Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-DETAIL-1',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->subDay()->toDateString(),
        'queue_number' => 'LK-010',
        'status' => QueueStatus::Completed->value,
        'called_at' => now()->subDay()->setTime(10, 0),
        'completed_at' => now()->subDay()->setTime(10, 20),
    ]);

    $report = Report::create([
        'created_by' => $adminFo->id,
        'title' => 'Laporan Detail Nomor HP',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'data_summary' => [
            'total_visitors' => 1,
            'completed_count' => 1,
            'skipped_count' => 0,
            'attendance_rate' => 100.0,
            'avg_service_time' => 20.0,
            'avg_waiting_time' => 0.0,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->actingAs($adminFo)->get(route('admin.fo.reports.show', $report));

    $response->assertSuccessful();
    $response->assertSee('Nomor HP');
    $response->assertSee('081355512345');
    $response->assertSee('Budi Santoso');
});

test('super admin pdf export includes full visitor phone number', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
        'name' => 'Siti Aminah',
        'no_telp' => '081299988877',
    ]);

    $department = Department::create([
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DK',
        'nomor_loket' => '03',
    ]);

    $startDate = now()->subDays(2)->toDateString();
    $endDate = now()->toDateString();

    $queue = Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-PDF-1',
        'purpose' => 'Surat Sehat',
        'session_name' => 'Siang',
        'booking_date' => now()->subDay()->toDateString(),
        'queue_number' => 'DK-005',
        'status' => QueueStatus::Completed->value,
        'called_at' => now()->subDay()->setTime(14, 0),
        'completed_at' => now()->subDay()->setTime(14, 25),
    ]);
    $queue->load(['user', 'department']);

    $report = Report::create([
        'created_by' => $superAdmin->id,
        'title' => 'Laporan PDF Nomor HP',
        'start_date' => $startDate,
        'end_date' => $endDate,
        'data_summary' => [
            'total_visitors' => 1,
            'completed_count' => 1,
            'skipped_count' => 0,
            'attendance_rate' => 100.0,
            'avg_service_time' => 25.0,
            'avg_waiting_time' => 0.0,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Terkirim',
    ]);

    $filename = 'laporan-antrean-'.$startDate.'-to-'.$endDate.'.pdf';

    $response = $this->actingAs($superAdmin)->get(route('admin.reports.export.pdf', $report));

    $response->assertSuccessful();
    $response->assertDownload($filename);

    $this->view('super_admin.reports.pdf', [
        'report' => $report,
        'queues' => collect([$queue]),
    ])
        ->assertSee('Nomor HP')
        ->assertSee('081299988877')
        ->assertSee('Siti Aminah');
});
