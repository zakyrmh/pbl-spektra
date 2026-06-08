<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('operator with no counter is shown warning page', function () {
    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = test()->actingAs($operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertSee('Loket Belum Ditugaskan');
});

test('operator with counter can view the dashboard and see their counter info', function () {
    $dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
    ]);

    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket 01',
        'status' => 'aktif',
    ]);

    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $counter->department_id,
    ]);

    $response = test()->actingAs($operator)->get(route('antrean.index'));

    $response->assertStatus(200);
    $response->assertSee('Loket Loket 01');
    $response->assertSee('Disdukcapil');
});

test('operator can update their counter status', function () {
    $dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
    ]);

    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket 01',
        'status' => 'aktif',
    ]);

    /** @var User $operator */
    $operator = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $counter->department_id,
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

test('operator can call next queue and complete it', function () {
    Mail::fake();
    Event::fake();

    $dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
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
        'departments_id' => $counter->department_id,
    ]);

    $queue = Queue::create([
        'counter_id' => $counter->id,
        'service_id' => $service->id,
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

test('operator cannot manage queue belonging to another department', function () {
    $deptA = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $deptB = Department::create(['name' => 'Samsat', 'inisial' => 'SMST']);

    $counterA = Counter::create(['department_id' => $deptA->id, 'name' => 'Loket DDK']);
    $counterB = Counter::create(['department_id' => $deptB->id, 'name' => 'Loket SMST']);

    /** @var User $operatorA */
    $operatorA = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $counterA->department_id,
    ]);

    $queueB = Queue::create([
        'counter_id' => $counterB->id,
        'queue_number' => 'SMST-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    // Coba panggil antrean milik loket B dengan operator A
    $response = test()->actingAs($operatorA)->post(route('gerai.call', $queueB->id));

    $response->assertStatus(403);
});
