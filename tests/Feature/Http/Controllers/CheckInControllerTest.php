<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminFo;

    protected User $citizen;

    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminFo = User::factory()->create([
            'role' => UserRole::AdminFo,
        ]);

        $this->citizen = User::factory()->create([
            'role' => UserRole::Pengunjung,
            'nik' => '1373021408990002',
            'name' => 'Budi Santoso',
        ]);

        $this->department = Department::create([
            'name' => 'Disdukcapil',
            'inisial' => 'DDK',
            'nomor_loket' => '23',
            'is_open' => true,
        ]);
    }

    public function test_guest_cannot_access_fo_checkin_page(): void
    {
        $response = $this->get(route('admin.fo.checkin'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_fo_can_access_fo_checkin_page(): void
    {
        $response = $this->actingAs($this->adminFo)->get(route('admin.fo.checkin'));
        $response->assertStatus(200);
    }

    public function test_verify_with_valid_booking_code_uuid(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Cetak KTP-el',
            'session_name' => 'Sesi 1',
        ]);

        // Web Check-in
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.verify'), [
            'booking_code' => $uuid,
        ]);
        $response->assertStatus(200);
        $response->assertViewHas('booking');

        // API Check-in
        $apiResponse = $this->actingAs($this->adminFo)->getJson(route('api.fo.bookings.verify', ['code' => $uuid]));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment([
            'booking_code' => $uuid,
        ]);
    }

    public function test_verify_with_valid_nik_smart_search(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Cetak KTP-el',
            'session_name' => 'Sesi 1',
        ]);

        // Web check-in using NIK instead of booking code
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.verify'), [
            'booking_code' => '1373021408990002',
        ]);
        $response->assertStatus(200);
        $response->assertViewHas('booking');
        $this->assertEquals($uuid, $response->viewData('booking')->booking_code);

        // API verify using NIK
        $apiResponse = $this->actingAs($this->adminFo)->getJson(route('api.fo.bookings.verify', ['code' => '1373021408990002']));
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonFragment([
            'booking_code' => $uuid,
        ]);
    }

    public function test_verify_prioritizes_booked_status_for_nik_search(): void
    {
        // 1. Create a completed booking for the citizen
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d',
            'booking_date' => now()->subDay()->toDateString(),
            'status' => QueueStatus::Completed->value,
            'purpose' => 'Layanan Lama',
            'session_name' => 'Sesi 1',
        ]);

        // 2. Create a booked booking for the citizen
        $activeUuid = 'f81d4fae-7dec-11d0-a765-00a0c91e6bf6';
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $activeUuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Layanan Aktif Baru',
            'session_name' => 'Sesi 1',
        ]);

        // Verify that searching by NIK retrieves the active 'Booked' queue, not the completed one
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.verify'), [
            'booking_code' => '1373021408990002',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($activeUuid, $response->viewData('booking')->booking_code);
    }

    public function test_verify_falls_back_to_latest_booking_if_no_booked_queue_exists(): void
    {
        $completedUuid = 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d';
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $completedUuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Completed->value,
            'purpose' => 'Sudah Selesai',
            'session_name' => 'Sesi 1',
        ]);

        // Searching by NIK should return the latest completed queue so we redirect to error/warning page
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.verify'), [
            'booking_code' => '1373021408990002',
        ]);
        // Since it's completed, it redirects back with warning
        $response->assertStatus(302);
        $response->assertSessionHas('warning');
    }

    public function test_verify_returns_error_if_not_found(): void
    {
        // Web
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'non-existent-code-or-nik',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // API
        $apiResponse = $this->actingAs($this->adminFo)->getJson(route('api.fo.bookings.verify', ['code' => 'non-existent']));
        $apiResponse->assertStatus(404);
    }

    public function test_checkin_fails_if_nik_already_has_active_queue_today(): void
    {
        // 1. Create an active (Checked-In) queue today for the citizen
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d',
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::CheckedIn->value,
            'purpose' => 'Layanan Lama',
            'session_name' => 'Sesi 1',
            'queue_number' => 'DDK-001',
        ]);

        // 2. Create another booked booking today
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $booking = Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Layanan Aktif Baru',
            'session_name' => 'Sesi 1',
        ]);

        // 3. Try to approve check-in
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.approve', $booking));
        $response->assertRedirect(route('admin.fo.checkin'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Warga dengan NIK ini sudah memiliki antrean aktif hari ini untuk instansi yang sama.', session('error'));
    }

    public function test_checkin_fails_if_daily_quota_is_full(): void
    {
        // 1. Set daily quota limit to 1
        Setting::setVal('daily_quota_limit', '1');

        // 2. Create one active queue today
        Queue::create([
            'user_id' => User::factory()->create()->id,
            'department_id' => $this->department->id,
            'booking_code' => 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d',
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::CheckedIn->value,
            'purpose' => 'Layanan Lama',
            'session_name' => 'Sesi 1',
            'queue_number' => 'DDK-001',
        ]);

        // 3. Create another booked booking today
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $booking = Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Layanan Aktif Baru',
            'session_name' => 'Sesi 1',
        ]);

        // 4. Try to approve check-in
        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.approve', $booking));
        $response->assertRedirect(route('admin.fo.checkin'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Kuota layanan untuk hari ini telah penuh', session('error'));
    }

    public function test_walkin_fails_if_daily_quota_is_full(): void
    {
        // 1. Set daily quota limit to 1
        Setting::setVal('daily_quota_limit', '1');

        // 2. Create one active queue today
        Queue::create([
            'user_id' => User::factory()->create()->id,
            'department_id' => $this->department->id,
            'booking_code' => 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d',
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::CheckedIn->value,
            'purpose' => 'Layanan Lama',
            'session_name' => 'Sesi 1',
            'queue_number' => 'DDK-001',
        ]);

        // 3. Try to issue walk-in ticket
        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.queues.walkin'), [
            'nik' => '9999888877776666',
            'name' => 'John Doe',
            'phone' => '081234567890',
            'department_id' => $this->department->id,
            'purpose' => 'Permohonan Layanan Baru',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => 'Kuota layanan untuk hari ini telah penuh',
        ]);
    }

    public function test_fo_admin_can_retrieve_unread_booking_notifications(): void
    {
        // 1. Create a notification for the FO admin
        $notification = Notification::create([
            'user_id' => $this->adminFo->id,
            'title' => 'Booking Baru Masuk',
            'message' => 'Pengunjung Jane Doe membuat booking online baru.',
        ]);

        // 2. Fetch notifications
        $response = $this->actingAs($this->adminFo)->getJson(route('api.fo.notifications.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'unread_count',
        ]);
        $response->assertJsonFragment([
            'id' => $notification->id,
            'title' => 'Booking Baru Masuk',
            'message' => 'Pengunjung Jane Doe membuat booking online baru.',
        ]);
    }

    public function test_fo_admin_can_mark_notification_as_read(): void
    {
        // 1. Create a notification
        $notification = Notification::create([
            'user_id' => $this->adminFo->id,
            'title' => 'Booking Baru Masuk',
            'message' => 'Pengunjung Jane Doe membuat booking online baru.',
        ]);

        // 2. Mark it as read
        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.notifications.read', ['id' => $notification->id]));
        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        // 3. Assert notification is marked read in database
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_unauthenticated_user_cannot_access_fo_notifications(): void
    {
        $response = $this->getJson(route('api.fo.notifications.index'));
        $response->assertStatus(401);
    }

    public function test_scan_qr_code_successfully_checks_in(): void
    {
        $uuid = 'a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d';
        $queue = Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Cetak KTP-el',
            'session_name' => 'Sesi 1',
        ]);

        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.scan-qr'), [
            'code' => $uuid,
            'status' => 'Checked-In',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => true,
            'booking_code' => $uuid,
            'status' => 'Checked-In',
            'user_name' => 'Budi Santoso',
        ]);

        $this->assertEquals(QueueStatus::CheckedIn->value, $queue->fresh()->status->value ?? $queue->fresh()->status);
    }

    public function test_scan_qr_code_fails_validation_if_code_missing(): void
    {
        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.scan-qr'), [
            'status' => 'Checked-In',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }

    public function test_scan_qr_code_fails_if_booking_not_found(): void
    {
        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.scan-qr'), [
            'code' => 'non-existent-uuid',
            'status' => 'Checked-In',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Tiket/booking tidak ditemukan.',
        ]);
    }

    public function test_scan_qr_code_fails_if_already_checked_in(): void
    {
        $uuid = 'already-checked-in-uuid';
        Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::CheckedIn->value,
            'purpose' => 'Cetak KTP-el',
            'session_name' => 'Sesi 1',
        ]);

        $response = $this->actingAs($this->adminFo)->postJson(route('api.fo.scan-qr'), [
            'code' => $uuid,
            'status' => 'Checked-In',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Tiket ini sudah melakukan check-in.',
        ]);
    }

    public function test_fo_admin_can_approve_checkin_and_update_priority(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $booking = Queue::create([
            'user_id' => $this->citizen->id,
            'department_id' => $this->department->id,
            'booking_code' => $uuid,
            'booking_date' => now()->toDateString(),
            'status' => QueueStatus::Booked->value,
            'purpose' => 'Cetak KTP-el',
            'session_name' => 'Sesi 1',
        ]);

        $response = $this->actingAs($this->adminFo)->post(route('admin.fo.checkin.approve', $booking), [
            'is_priority' => '1',
        ]);

        $response->assertRedirect(route('admin.fo.checkin'));
        $booking->refresh();
        $this->assertEquals(QueueStatus::CheckedIn->value, $booking->status->value ?? $booking->status);
        $this->assertTrue((bool) $booking->is_priority);
        $this->assertEquals('P-001', $booking->queue_number);
        $this->assertTrue((bool) $this->citizen->fresh()->is_priority);
    }
}
