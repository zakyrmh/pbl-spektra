<?php

use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('visitor can view notification list', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    Notification::create([
        'user_id' => $visitor->id,
        'title' => 'Test Notification',
        'message' => 'This is a test notification.',
    ]);

    $response = test()->actingAs($visitor)->get(route('notifications.index'));

    $response->assertStatus(200);
    $response->assertSee('Test Notification');
    $response->assertSee('This is a test notification.');
});

test('viewing notification marks it read and redirects to feedback if completed queue exists', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
    ]);

    $counter = Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket 01',
    ]);

    $service = Service::create([
        'department_id' => $dept->id,
        'name' => 'Cetak KTP',
    ]);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Completed',
        'booking_code' => 'TEST-UUID-1234',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $notif = Notification::create([
        'user_id' => $visitor->id,
        'title' => 'Pelayanan Selesai',
        'message' => 'Pelayanan selesai.',
    ]);

    $response = test()->actingAs($visitor)->get(route('notifications.show', $notif->id));

    $notif->refresh();
    expect($notif->read_at)->not->toBeNull();
    $response->assertRedirect(route('feedback.create', ['queue_id' => $queue->id]));
});

test('visitor can access feedback form for their completed queue', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Completed',
        'booking_code' => 'TEST-UUID-1234',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $response = test()->actingAs($visitor)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertStatus(200);
    $response->assertSee('DDK-001');
    $response->assertSee('Disdukcapil');
    $response->assertSee('Cetak KTP');
});

test('visitor cannot access feedback form for another user completed queue', function () {
    /** @var User $visitor1 */
    $visitor1 = User::factory()->create(['role' => 'pengunjung']);
    /** @var User $visitor2 */
    $visitor2 = User::factory()->create(['role' => 'pengunjung']);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    $booking = Booking::create([
        'user_id' => $visitor2->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Completed',
        'booking_code' => 'TEST-UUID-1234',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $response = test()->actingAs($visitor1)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengulas antrean ini.');
});

test('visitor can submit feedback and it is saved with activity log', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Completed',
        'booking_code' => 'TEST-UUID-1234',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    $response = test()->actingAs($visitor)->post(route('feedback.store'), [
        'queue_id' => $queue->id,
        'rating' => 5,
        'comment' => 'Pelayanan sangat memuaskan.',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Feedback berhasil dikirim, terima kasih!');

    test()->assertDatabaseHas('feedbacks', [
        'queue_id' => $queue->id,
        'user_id' => $visitor->id,
        'rating' => 5,
        'comment' => 'Pelayanan sangat memuaskan.',
    ]);

    test()->assertDatabaseHas('activity_logs', [
        'action' => 'SUBMIT_FEEDBACK',
        'user_id' => $visitor->id,
    ]);
});

test('visitor cannot submit feedback twice for the same queue', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 01']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Cetak KTP']);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Completed',
        'booking_code' => 'TEST-UUID-1234',
    ]);

    $queue = Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    Feedback::create([
        'queue_id' => $queue->id,
        'user_id' => $visitor->id,
        'rating' => 4,
        'comment' => 'Review pertama.',
    ]);

    // Try submitting second time via GET
    $responseGet = test()->actingAs($visitor)->get(route('feedback.create', ['queue_id' => $queue->id]));
    $responseGet->assertRedirect(route('dashboard'));
    $responseGet->assertSessionHas('warning', 'Akses Ditolak! Anda sudah mengisi ulasan untuk layanan ini.');

    // Try submitting second time via POST
    $responsePost = test()->actingAs($visitor)->post(route('feedback.store'), [
        'queue_id' => $queue->id,
        'rating' => 5,
        'comment' => 'Review kedua.',
    ]);
    $responsePost->assertRedirect(route('dashboard'));
    $responsePost->assertSessionHas('warning', 'Gagal: Ulasan untuk layanan ini sudah pernah dikirim.');
});

test('front office can submit feedback on behalf of walk-in visitor', function () {
    /** @var User $fo */
    $fo = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $dept = Department::create(['name' => 'Front Office', 'inisial' => 'FO']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket FO']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'Informasi']);

    $queue = Queue::create([
        'booking_id' => null,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'FO-001',
        'status' => 'Completed',
        'queue_date' => now()->toDateString(),
    ]);

    // FO accesses create form
    $responseGet = test()->actingAs($fo)->get(route('feedback.create', ['queue_id' => $queue->id]));
    $responseGet->assertStatus(200);

    // FO submits feedback
    $responsePost = test()->actingAs($fo)->post(route('feedback.store'), [
        'queue_id' => $queue->id,
        'rating' => 4,
        'comment' => 'Warga puas.',
    ]);

    $responsePost->assertRedirect(route('dashboard'));
    $responsePost->assertSessionHas('success', 'Feedback berhasil dikirim, terima kasih!');

    test()->assertDatabaseHas('feedbacks', [
        'queue_id' => $queue->id,
        'user_id' => $fo->id,
        'rating' => 4,
        'comment' => 'Warga puas.',
    ]);
});
