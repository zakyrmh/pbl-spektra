<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected to login when accessing fo monitor', function () {
    $response = $this->get(route('admin.fo.monitor'));
    $response->assertRedirect(route('login'));
});

test('admin fo can access fo monitor view and see departments data', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-0001',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Sesi 1',
        'booking_date' => Carbon::today(),
        'queue_number' => 'LK-001',
        'status' => 'Checked-In', // waiting
    ]);

    $response = $this->actingAs($user)->get(route('admin.fo.monitor'));

    $response->assertStatus(200);
    $response->assertViewIs('admin.fo.monitor');
    $response->assertViewHas('departments');
    $response->assertViewHas('metrics');

    $departments = $response->viewData('departments');
    expect($departments)->toHaveCount(1);
    expect($departments->first()->waitingCount)->toBe(1);
    expect($departments->first()->servingCount)->toBe(0);
});

test('admin fo can fetch fo monitor data as json', function () {
    $user = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-0001',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Sesi 1',
        'booking_date' => Carbon::today(),
        'queue_number' => 'LK-001',
        'status' => 'Serving',
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('admin.fo.monitor', ['json' => 'true']));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'metrics' => ['total_waiting', 'total_serving', 'average_wait_time'],
        'departments' => [
            '*' => [
                'id',
                'name',
                'inisial',
                'description',
                'waiting_count',
                'serving_count',
                'density',
                'density_class',
                'density_dot',
            ],
        ],
    ]);
});

test('public display can be accessed by guests', function () {
    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $response = $this->get(route('display.index'));

    $response->assertStatus(200);
    $response->assertViewIs('public.display');
    $response->assertViewHas('departments');
});

test('public display api yields clean formatted counters data', function () {
    $department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
        'description' => 'Layanan Dukcapil',
        'is_open' => true,
    ]);

    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    Queue::create([
        'user_id' => $visitor->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-0001',
        'purpose' => 'Ambil KTP',
        'session_name' => 'Sesi 1',
        'booking_date' => Carbon::today(),
        'queue_number' => 'LK-001',
        'status' => 'Serving',
    ]);

    $response = $this->getJson(route('display.data'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'counters' => [
            '*' => [
                'counter_id',
                'counter_name',
                'department_name',
                'status',
                'active_number',
                'active_status',
                'called_at',
            ],
        ],
        'marquee_text',
        'marquee_active',
    ]);

    $data = $response->json();
    expect($data['counters'][0]['active_number'])->toBe('LK-001');
    expect($data['counters'][0]['active_status'])->toBe('Serving');
});
