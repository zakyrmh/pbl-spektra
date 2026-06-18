<?php

use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $this->otherUser = User::factory()->create([
        'role' => 'pengunjung',
    ]);
});

test('guest is redirected to login from notification index', function () {
    $this->get(route('notifications.index'))->assertRedirect(route('login'));
});

test('guest is redirected to login from notification show', function () {
    $notification = Notification::create([
        'user_id' => $this->user->id,
        'title' => 'Test Notification',
        'message' => 'This is a test notification message.',
    ]);

    $this->get(route('notifications.show', $notification->id))->assertRedirect(route('login'));
});

test('authenticated user can view their notifications', function () {
    $notification1 = Notification::create([
        'user_id' => $this->user->id,
        'title' => 'User Notification 1',
        'message' => 'Notification content 1',
    ]);

    $notification2 = Notification::create([
        'user_id' => $this->otherUser->id,
        'title' => 'Other Notification',
        'message' => 'Notification content other',
    ]);

    $response = $this->actingAs($this->user)->get(route('notifications.index'));

    $response->assertStatus(200);
    $response->assertViewIs('notifications.index');
    $response->assertViewHas('notifications');

    $viewNotifications = $response->viewData('notifications');
    expect($viewNotifications->count())->toBe(1);
    expect($viewNotifications->first()->id)->toBe($notification1->id);
});

test('authenticated user cannot view another users notification', function () {
    $notification = Notification::create([
        'user_id' => $this->otherUser->id,
        'title' => 'Other Notification',
        'message' => 'Notification content other',
    ]);

    $response = $this->actingAs($this->user)->get(route('notifications.show', $notification->id));

    $response->assertStatus(403);
});

test('viewing notification marks it as read and redirects to dashboard if no completed unreviewed queues exist', function () {
    $notification = Notification::create([
        'user_id' => $this->user->id,
        'title' => 'User Notification',
        'message' => 'Notification content',
        'read_at' => null,
    ]);

    expect($notification->read_at)->toBeNull();

    $response = $this->actingAs($this->user)->get(route('notifications.show', $notification->id));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('info', 'Notifikasi telah ditandai sebagai dibaca.');

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('viewing notification redirects to feedback form if a completed unreviewed queue exists', function () {
    $notification = Notification::create([
        'user_id' => $this->user->id,
        'title' => 'User Notification',
        'message' => 'Notification content',
        'read_at' => null,
    ]);

    $department = Department::create([
        'name' => 'Dispenduk',
        'inisial' => 'DPK',
        'nomor_loket' => '01',
    ]);

    $queue = Queue::create([
        'user_id' => $this->user->id,
        'department_id' => $department->id,
        'booking_code' => 'BK-12345',
        'purpose' => 'KTP',
        'session_name' => 'Pagi',
        'booking_date' => now()->toDateString(),
        'queue_number' => 1,
        'status' => 'Completed',
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($this->user)->get(route('notifications.show', $notification->id));

    $response->assertRedirect(route('feedback.create', ['queue_id' => $queue->id]));
    $response->assertSessionHas('info', 'Silakan isi ulasan untuk pelayanan Anda.');

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});
