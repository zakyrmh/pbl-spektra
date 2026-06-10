<?php

use App\Events\QueueCreated;
use App\Models\ActivityLog;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed basic department, service, and counter
    $this->department = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Kependudukan dan Catatan Sipil',
    ]);

    $this->service = Service::create([
        'department_id' => $this->department->id,
        'name' => 'Cetak KTP',
        'description' => 'Layanan cetak KTP-el baru',
    ]);

    $this->counter = Counter::create([
        'department_id' => $this->department->id,
        'name' => 'Loket Disdukcapil 1',
        'location' => 'Lantai 1 Gerai 23',
        'status' => 'aktif',
        'counter_number' => 1,
    ]);

    // Setup visitor user for auth checks
    $this->visitorUser = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    // Setup FO admin
    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);
});

test('guests are redirected to login when accessing walkin ticket routes', function () {
    $this->get(route('admin.fo.ticket.create'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.ticket.store'), [])->assertRedirect(route('login'));
});

test('visitor role cannot access walkin ticket routes (403)', function () {
    $this->actingAs($this->visitorUser)->get(route('admin.fo.ticket.create'))->assertStatus(403);
    $this->actingAs($this->visitorUser)->post(route('admin.fo.ticket.store'), [])->assertStatus(403);
});

test('FO admin can view the walkin ticket creation form', function () {
    $response = $this->actingAs($this->foAdmin)->get(route('admin.fo.ticket.create'));
    $response->assertStatus(200);
    $response->assertSee('Penerbitan Karcis Walk-In');
    $response->assertSee('Disdukcapil');
});

test('FO admin can successfully issue a walkin ticket', function () {
    Event::fake();

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.ticket.create'))
        ->post(route('admin.fo.ticket.store'), [
            'department_id' => $this->department->id,
            'service_id' => $this->service->id,
            'counter_id' => $this->counter->id,
            'name' => 'Budi Santoso',
            'nik' => '9876543210123456',
            'phone' => '081234567890',
            'purpose' => 'Mengurus pembuatan KTP elektronik baru yang rusak.',
        ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('ticket');

    // Verify Visitor created
    $visitor = Visitor::where('nik', '9876543210123456')->first();
    expect($visitor)->not->toBeNull();
    expect($visitor->name)->toBe('Budi Santoso');

    // Verify Queue created
    $queue = Queue::where('visitor_id', $visitor->id)->first();
    expect($queue)->not->toBeNull();
    expect($queue->queue_number)->toBe('DDK-001');
    expect($queue->status)->toBe('Waiting');

    // Verify event dispatched
    Event::assertDispatched(QueueCreated::class, function ($event) use ($queue) {
        return $event->queue->id === $queue->id;
    });

    // Verify activity log
    $log = ActivityLog::where('action', 'WALKIN_TICKET')->first();
    expect($log)->not->toBeNull();
    expect($log->user_id)->toBe($this->foAdmin->id);
    expect($log->description)->toContain('DDK-001');
});

test('walkin ticket validation rejects invalid formats', function () {
    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.ticket.create'))
        ->post(route('admin.fo.ticket.store'), [
            'department_id' => '',
            'service_id' => '',
            'counter_id' => '',
            'name' => 'Bu',
            'nik' => '12345',
            'phone' => '123',
            'purpose' => 'Opsi',
        ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHasErrors(['department_id', 'service_id', 'counter_id', 'name', 'nik', 'phone', 'purpose']);
});

test('cannot issue duplicate walkin tickets for the same service and date (BR-07)', function () {
    // Create first walk-in visitor & queue ticket
    $visitor = Visitor::create([
        'name' => 'Budi Santoso',
        'nik' => '9876543210123456',
        'phone' => '081234567890',
        'purpose' => 'Mengurus KTP',
    ]);

    Queue::create([
        'visitor_id' => $visitor->id,
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    // Try issuing same ticket again
    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.ticket.create'))
        ->post(route('admin.fo.ticket.store'), [
            'department_id' => $this->department->id,
            'service_id' => $this->service->id,
            'counter_id' => $this->counter->id,
            'name' => 'Budi Santoso',
            'nik' => '9876543210123456',
            'phone' => '081234567890',
            'purpose' => 'Mengurus KTP',
        ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHasErrors(['nik']);

    $errors = session('errors');
    expect($errors->first('nik'))->toContain('Pengunjung dengan NIK ini sudah memiliki antrean aktif hari ini untuk layanan yang sama.');
});

test('can issue walkin tickets for same NIK but different service today', function () {
    // Create first walk-in visitor & queue ticket
    $visitor = Visitor::create([
        'name' => 'Budi Santoso',
        'nik' => '9876543210123456',
        'phone' => '081234567890',
        'purpose' => 'Mengurus KTP',
    ]);

    Queue::create([
        'visitor_id' => $visitor->id,
        'counter_id' => $this->counter->id,
        'service_id' => $this->service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    // Create a different service
    $otherService = Service::create([
        'department_id' => $this->department->id,
        'name' => 'Cetak KK',
        'description' => 'Layanan cetak KK baru',
    ]);

    // Issue for other service is allowed
    $response = $this->actingAs($this->foAdmin)
        ->post(route('admin.fo.ticket.store'), [
            'department_id' => $this->department->id,
            'service_id' => $otherService->id,
            'counter_id' => $this->counter->id,
            'name' => 'Budi Santoso',
            'nik' => '9876543210123456',
            'phone' => '081234567890',
            'purpose' => 'Mengurus KK',
        ]);

    $response->assertRedirect(route('admin.fo.ticket.create'));
    $response->assertSessionHas('success');
});
