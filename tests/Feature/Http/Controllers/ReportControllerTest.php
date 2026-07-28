<?php

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
