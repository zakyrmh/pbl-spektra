<?php

use App\Events\Public\FeedbackSubmitted;
use App\Models\Department;
use App\Models\Feedback;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $this->otherUser = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $this->adminFo = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    $this->adminGerai = User::factory()->create([
        'role' => 'admin_gerai',
    ]);

    $this->department = Department::create([
        'name' => 'Layanan Kependudukan',
        'inisial' => 'LK',
        'nomor_loket' => '01',
    ]);
});

test('guest is redirected to login from feedback create', function () {
    $this->get(route('feedback.create'))->assertRedirect(route('login'));
});

test('guest is redirected to login from feedback store', function () {
    $this->post(route('feedback.store'), [])->assertRedirect(route('login'));
});

test('submitting feedback fails if queue_id is missing or invalid', function () {
    $response = $this->actingAs($this->user)
        ->from(route('feedback.create', ['queue_id' => 999]))
        ->post(route('feedback.store'), [
            'queue_id' => '',
            'rating' => 5,
        ]);

    $response->assertRedirect(route('feedback.create', ['queue_id' => 999]));
    $response->assertSessionHasErrors(['queue_id']);
});

test('submitting feedback fails if rating is missing or not between 1-5', function () {
    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    // Missing rating
    $response1 = $this->actingAs($this->user)
        ->from(route('feedback.create', ['queue_id' => $queue->id]))
        ->post(route('feedback.store'), [
            'queue_id' => $queue->id,
            'rating' => '',
        ]);
    $response1->assertSessionHasErrors(['rating']);

    // Invalid rating
    $response2 = $this->actingAs($this->user)
        ->from(route('feedback.create', ['queue_id' => $queue->id]))
        ->post(route('feedback.store'), [
            'queue_id' => $queue->id,
            'rating' => 6,
        ]);
    $response2->assertSessionHasErrors(['rating']);
});

test('user cannot give feedback for non-completed queue', function () {
    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Hold',
    ]);

    $response = $this->actingAs($this->user)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Ulasan hanya dapat diberikan untuk pelayanan yang telah selesai.');
});

test('user cannot review already reviewed queue', function () {
    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    Feedback::create([
        'queue_id' => $queue->id,
        'user_id' => $this->user->id,
        'rating' => 5,
        'comment' => 'Great',
    ]);

    $response = $this->actingAs($this->user)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('warning', 'Akses Ditolak! Anda sudah mengisi ulasan untuk layanan ini.');
});

test('visitor cannot review another user\'s queue', function () {
    $queue = Queue::create([
        'user_id' => $this->otherUser->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Anda tidak memiliki akses untuk mengulas antrean ini.');
});

test('unauthorized role cannot review any queue', function () {
    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->adminGerai)->get(route('feedback.create', ['queue_id' => $queue->id]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error', 'Peran Anda tidak diizinkan untuk mengisi ulasan.');
});

test('admin fo can only review walk-in queue', function () {
    // Online queue
    $onlineQueue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Umum',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response1 = $this->actingAs($this->adminFo)->get(route('feedback.create', ['queue_id' => $onlineQueue->id]));
    $response1->assertRedirect(route('dashboard'));
    $response1->assertSessionHas('error', 'Petugas Front Office hanya dapat mengisi ulasan untuk pengunjung walk-in.');

    // Walk-in queue
    $walkInQueue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'WI-LK-20260612-ABCDEF',
        'purpose' => 'KTP',
        'session_name' => 'Walk-In',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-002',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response2 = $this->actingAs($this->adminFo)->get(route('feedback.create', ['queue_id' => $walkInQueue->id]));
    $response2->assertStatus(200);
    $response2->assertViewIs('feedback.create');
});

test('visitor can successfully submit feedback for their own completed queue', function () {
    Event::fake();

    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->post(route('feedback.store'), [
        'queue_id' => $queue->id,
        'rating' => 5,
        'comment' => 'Sangat memuaskan dan cepat.',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Feedback berhasil dikirim, terima kasih!');

    $this->assertDatabaseHas('feedbacks', [
        'queue_id' => $queue->id,
        'user_id' => $this->user->id,
        'rating' => 5,
        'comment' => 'Sangat memuaskan dan cepat.',
    ]);

    Event::assertDispatched(FeedbackSubmitted::class, function ($event) use ($queue) {
        return $event->feedback->rating === 5 && $event->queue->id === $queue->id;
    });
});

test('submitting feedback triggers listener to record activity log', function () {
    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $this->department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 'LK-001',
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->post(route('feedback.store'), [
        'queue_id' => $queue->id,
        'rating' => 4,
        'comment' => 'Bagus.',
    ]);

    $response->assertRedirect(route('dashboard'));

    $feedback = Feedback::where('queue_id', $queue->id)->first();
    expect($feedback)->not->toBeNull();

    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->user->id,
        'event' => 'SUBMIT_FEEDBACK',
        'description' => 'Pengunjung memberikan rating bintang 4 untuk nomor antrean LK-001.',
    ]);
});
