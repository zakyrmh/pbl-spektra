<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public index page renders successfully with fallback values when database is empty', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertViewIs('pages.index');
    $response->assertViewHas('totalInstansi', 0);
    $response->assertViewHas('totalLayanan', 0);
    $response->assertViewHas('rataWaktuTunggu', '15 Menit');
});

test('public index page calculates total departments and services when database is populated', function () {
    // Create 2 departments
    Department::create(['name' => 'Dept A', 'inisial' => 'DA']);
    Department::create(['name' => 'Dept B', 'inisial' => 'DB']);

    // Create 3 services
    $dept = Department::first();
    Service::create(['department_id' => $dept->id, 'name' => 'Service 1']);
    Service::create(['department_id' => $dept->id, 'name' => 'Service 2']);
    Service::create(['department_id' => $dept->id, 'name' => 'Service 3']);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertViewHas('totalInstansi', 2);
    $response->assertViewHas('totalLayanan', 3);
    $response->assertViewHas('rataWaktuTunggu', '15 Menit');
});

test('public index page calculates average waiting time from completed queues', function () {
    Department::create(['name' => 'Dept A', 'inisial' => 'DA']);
    $dept = Department::first();
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Service 1']);
    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket A',
        'status' => 'aktif',
    ]);

    // Create a completed queue with a wait time of 25 minutes
    $q1 = new Queue([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DA-001',
        'status' => 'Completed',
        'queue_date' => '2026-06-09',
    ]);
    $q1->created_at = Carbon::parse('2026-06-09 10:00:00');
    $q1->called_at = Carbon::parse('2026-06-09 10:25:00');
    $q1->save();

    // Create another completed queue with a wait time of 15 minutes
    $q2 = new Queue([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DA-002',
        'status' => 'Completed',
        'queue_date' => '2026-06-09',
    ]);
    $q2->created_at = Carbon::parse('2026-06-09 10:00:00');
    $q2->called_at = Carbon::parse('2026-06-09 10:15:00');
    $q2->save();

    // Average wait time = (25 + 15) / 2 = 20 minutes
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertViewHas('rataWaktuTunggu', '20 Menit');
});

test('public check-queue page renders successfully', function () {
    $response = $this->get(route('public.check'));

    $response->assertStatus(200);
    $response->assertViewIs('pages.check-queue');
});

test('checking queue fails validation when code is missing', function () {
    $response = $this->post(route('public.check.process'), []);

    $response->assertSessionHasErrors(['code']);
});

test('checking queue redirects back with error when code is not found', function () {
    $response = $this->from(route('public.check'))
        ->post(route('public.check.process'), [
            'code' => 'NON-EXISTENT-CODE',
        ]);

    $response->assertRedirect(route('public.check'));
    $response->assertSessionHas('error', "Antrean dengan kode 'NON-EXISTENT-CODE' tidak ditemukan.");
});

test('checking queue by queue number displays queue details', function () {
    $dept = Department::create(['name' => 'Dept A', 'inisial' => 'DA']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Service 1']);
    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket A',
        'status' => 'aktif',
    ]);
    $queue = Queue::create([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DA-005',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    $response = $this->post(route('public.check.process'), [
        'code' => 'DA-005',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('pages.check-queue');
    $response->assertViewHas('searched', true);
    $response->assertViewHas('queue', function ($q) use ($queue) {
        return $q->id === $queue->id;
    });
});
