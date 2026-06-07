<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup Super Admin
    $this->superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    // Setup FO Admin
    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // Setup regular customer for queue
    $this->customer = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    // Seed master data
    $this->department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Kependudukan',
    ]);

    $this->service = Service::create([
        'department_id' => $this->department->id,
        'name' => 'KTP-el',
        'description' => 'Rekam KTP',
    ]);

    $this->counter = Counter::create([
        'department_id' => $this->department->id,
        'name' => 'Loket DDK 1',
        'location' => 'Lantai 1',
    ]);

    // Create completed queues on past dates
    $this->pastDate = Carbon::yesterday()->toDateString();

    $this->queue = Queue::create([
        'visitor_id' => null,
        'booking_id' => null,
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => $this->pastDate,
        'called_at' => Carbon::yesterday()->setHour(9)->setMinute(0)->toDateTimeString(),
        'completed_at' => Carbon::yesterday()->setHour(9)->setMinute(15)->toDateTimeString(),
    ]);

    DB::table('queues')->where('id', $this->queue->id)->update([
        'created_at' => Carbon::yesterday()->setHour(8)->setMinute(45)->toDateTimeString(),
    ]);
});

test('guests are redirected to login when accessing reports routes', function () {
    // FO Routes
    $this->get(route('admin.fo.reports.index'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.reports.store'))->assertRedirect(route('login'));

    // Super Admin Routes
    $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
});

test('customer cannot access reports routes', function () {
    // FO Routes
    $this->actingAs($this->customer)->get(route('admin.fo.reports.index'))->assertStatus(403);
    $this->actingAs($this->customer)->post(route('admin.fo.reports.store'))->assertStatus(403);

    // Super Admin Routes
    $this->actingAs($this->customer)->get(route('admin.reports.index'))->assertStatus(403);
});

test('front office admin cannot access super admin reports routes', function () {
    $this->actingAs($this->foAdmin)->get(route('admin.reports.index'))->assertStatus(403);
});

test('super admin cannot access front office reports routes', function () {
    $this->actingAs($this->superAdmin)->get(route('admin.fo.reports.index'))->assertStatus(403);
});

test('fo admin can view reports dashboard and create a new report draft', function () {
    $response = $this->actingAs($this->foAdmin)->get(route('admin.fo.reports.index'));
    $response->assertStatus(200);
    $response->assertSee('Kelola Laporan Kinerja');

    $responseStore = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.reports.index'))
        ->post(route('admin.fo.reports.store'), [
            'title' => 'Laporan Kemarin',
            'start_date' => $this->pastDate,
            'end_date' => $this->pastDate,
        ]);

    $responseStore->assertRedirect(route('admin.fo.reports.index'));

    $this->assertDatabaseHas('reports', [
        'title' => 'Laporan Kemarin',
        'status' => 'Belum Dikirim',
    ]);

    $report = Report::first();
    expect($report->data_summary['total_visitors'])->toBe(1);
    expect($report->data_summary['completed_count'])->toBe(1);
    expect($report->data_summary['avg_service_time'])->toEqual(15); // 9:00 to 9:15 = 15 mins
    expect($report->data_summary['avg_waiting_time'])->toEqual(15); // 8:45 to 9:00 = 15 mins
});

test('creating a report fails if no queues exist in the date range', function () {
    $noQueuesDate = Carbon::yesterday()->subDays(5)->toDateString();

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.reports.index'))
        ->post(route('admin.fo.reports.store'), [
            'title' => 'Laporan Kosong',
            'start_date' => $noQueuesDate,
            'end_date' => $noQueuesDate,
        ]);

    $response->assertRedirect(route('admin.fo.reports.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseEmpty('reports');
});

test('fo admin can update a report draft', function () {
    $report = Report::create([
        'created_by' => $this->foAdmin->id,
        'title' => 'Draf Laporan Awal',
        'start_date' => $this->pastDate,
        'end_date' => $this->pastDate,
        'data_summary' => [],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.reports.index'))
        ->put(route('admin.fo.reports.update', $report), [
            'title' => 'Draf Laporan Diperbarui',
            'start_date' => $this->pastDate,
            'end_date' => $this->pastDate,
        ]);

    $response->assertRedirect(route('admin.fo.reports.index'));

    $report->refresh();
    expect($report->title)->toBe('Draf Laporan Diperbarui');
    expect($report->data_summary)->not->toBeEmpty();
});

test('fo admin can delete a report draft', function () {
    $report = Report::create([
        'created_by' => $this->foAdmin->id,
        'title' => 'Laporan Mau Dihapus',
        'start_date' => $this->pastDate,
        'end_date' => $this->pastDate,
        'data_summary' => [],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.reports.index'))
        ->delete(route('admin.fo.reports.destroy', $report));

    $response->assertRedirect(route('admin.fo.reports.index'));
    $this->assertModelMissing($report);
});

test('fo admin can send report to super admin which locks it and notifies super admins', function () {
    $report = Report::create([
        'created_by' => $this->foAdmin->id,
        'title' => 'Laporan Siap Kirim',
        'start_date' => $this->pastDate,
        'end_date' => $this->pastDate,
        'data_summary' => [],
        'status' => 'Belum Dikirim',
    ]);

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.reports.index'))
        ->post(route('admin.fo.reports.send', $report));

    $response->assertRedirect(route('admin.fo.reports.index'));
    $response->assertSessionHas('success');

    $report->refresh();
    expect($report->status)->toBe('Terkirim');

    // Super Admin should be notified
    $notification = Notification::where('user_id', $this->superAdmin->id)->first();
    expect($notification)->not->toBeNull();
    expect($notification->title)->toContain('Laporan Kinerja Baru');

    // Attempting to edit sent report should fail
    $this->actingAs($this->foAdmin)
        ->put(route('admin.fo.reports.update', $report), [
            'title' => 'Coba Edit',
            'start_date' => $this->pastDate,
            'end_date' => $this->pastDate,
        ])
        ->assertRedirect(route('admin.fo.reports.index'))
        ->assertSessionHas('error');

    // Attempting to delete sent report should fail
    $this->actingAs($this->foAdmin)
        ->delete(route('admin.fo.reports.destroy', $report))
        ->assertRedirect(route('admin.fo.reports.index'))
        ->assertSessionHas('error');
});

test('super admin can view list of sent reports and detailed statistics', function () {
    $report = Report::create([
        'created_by' => $this->foAdmin->id,
        'title' => 'Laporan Bulanan',
        'start_date' => $this->pastDate,
        'end_date' => $this->pastDate,
        'data_summary' => [
            'total_visitors' => 1,
            'completed_count' => 1,
            'skipped_count' => 0,
            'attendance_rate' => 100,
            'avg_service_time' => 15,
            'avg_waiting_time' => 15,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Terkirim',
    ]);

    // View index
    $responseIndex = $this->actingAs($this->superAdmin)->get(route('admin.reports.index'));
    $responseIndex->assertStatus(200);
    $responseIndex->assertSee('Laporan Bulanan');

    // View show
    $responseShow = $this->actingAs($this->superAdmin)->get(route('admin.reports.show', $report));
    $responseShow->assertStatus(200);
    $responseShow->assertSee('Laporan Bulanan');
    $responseShow->assertSee('DDK-001'); // queue number from completed queue
});

test('super admin can download reports in excel and pdf formats', function () {
    $report = Report::create([
        'created_by' => $this->foAdmin->id,
        'title' => 'Laporan Ekspor',
        'start_date' => $this->pastDate,
        'end_date' => $this->pastDate,
        'data_summary' => [
            'total_visitors' => 1,
            'completed_count' => 1,
            'skipped_count' => 0,
            'attendance_rate' => 100,
            'avg_service_time' => 15,
            'avg_waiting_time' => 15,
            'per_department' => [],
            'daily_series' => [],
        ],
        'status' => 'Terkirim',
    ]);

    // Excel Export
    $responseExcel = $this->actingAs($this->superAdmin)->get(route('admin.reports.export.excel', $report));
    $responseExcel->assertStatus(200);
    $responseExcel->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    // PDF Export
    $responsePdf = $this->actingAs($this->superAdmin)->get(route('admin.reports.export.pdf', $report));
    $responsePdf->assertStatus(200);
    $responsePdf->assertHeader('content-type', 'application/pdf');
});
