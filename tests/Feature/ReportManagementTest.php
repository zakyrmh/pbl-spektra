<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('front office can view reports list', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $response = test()->actingAs($fo)->get(route('reports.index'));

    $response->assertStatus(200);
});

test('creating report fails if there is no queue in selected range', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $response = test()->actingAs($fo)->post(route('reports.store'), [
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Tidak ada data antrean pada tanggal tersebut.');
});

test('front office can create a draft report if queue exists', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    Queue::query()->create([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $response = test()->actingAs($fo)->post(route('reports.store'), [
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    test()->assertDatabaseHas('reports', [
        'created_by' => $fo->id,
        'status' => 'Belum Dikirim',
    ]);
});

test('front office can update a draft report', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    Queue::query()->create([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Test',
        'start_date' => now()->subDay()->toDateString(),
        'end_date' => now()->subDay()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Belum Dikirim',
    ]);

    $response = test()->actingAs($fo)->put(route('reports.update', $report->id), [
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response->assertRedirect();
    $report->refresh();
    expect($report->start_date->toDateString())->toBe(now()->toDateString());
});

test('front office can delete a draft report', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Test Hapus',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Belum Dikirim',
    ]);

    $response = test()->actingAs($fo)->delete(route('reports.destroy', $report->id));

    $response->assertRedirect(route('reports.index'));
    test()->assertDatabaseMissing('reports', ['id' => $report->id]);
});

test('front office can send a report, locking it and notifying super admins', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);
    /** @var User $superAdmin */
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Test Kirim',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Belum Dikirim',
    ]);

    $response = test()->actingAs($fo)->post(route('reports.send', $report->id));

    $response->assertRedirect(route('reports.index'));

    $report->refresh();
    expect($report->status)->toBe('Terkirim');
    expect($report->isLocked())->toBeTrue();

    test()->assertDatabaseHas('notifications', [
        'user_id' => $superAdmin->id,
        'title' => 'Laporan Baru Masuk',
    ]);
});

test('locked report cannot be edited, updated, or deleted', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Test Terkunci',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Terkirim',
    ]);

    // Try editing
    $editResponse = test()->actingAs($fo)->get(route('reports.edit', $report->id));
    $editResponse->assertRedirect(route('reports.index'));
    $editResponse->assertSessionHas('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');

    // Try updating
    $updateResponse = test()->actingAs($fo)->put(route('reports.update', $report->id), [
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);
    $updateResponse->assertRedirect(route('reports.index'));
    $updateResponse->assertSessionHas('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');

    // Try deleting
    $deleteResponse = test()->actingAs($fo)->delete(route('reports.destroy', $report->id));
    $deleteResponse->assertRedirect(route('reports.index'));
    $deleteResponse->assertSessionHas('warning', 'Laporan telah dikirim, data tidak dapat dimodifikasi.');
});

test('super admin can view list of sent reports and their detail', function () {
    /** @var User $superAdmin */
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Sent',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Terkirim',
    ]);

    $responseList = test()->actingAs($superAdmin)->get(route('admin.reports.index'));
    $responseList->assertStatus(200);
    $responseList->assertSee('Laporan Sent');

    $responseDetail = test()->actingAs($superAdmin)->get(route('admin.reports.show', $report->id));
    $responseDetail->assertStatus(200);
    $responseDetail->assertSee('Laporan Sent');
});

test('other roles cannot access reports', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create(['role' => 'pengunjung']);
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $report = Report::create([
        'created_by' => $fo->id,
        'title' => 'Laporan Rahasia',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'data_summary' => ['total_visitors' => 1],
        'status' => 'Terkirim',
    ]);

    // Visitor tries to access reports index
    test()->actingAs($visitor)->get(route('reports.index'))->assertStatus(403);

    // Visitor tries to access super admin reports index
    test()->actingAs($visitor)->get(route('admin.reports.index'))->assertStatus(403);

    // Visitor tries to access report details
    test()->actingAs($visitor)->get(route('admin.reports.show', $report->id))->assertStatus(403);
});
