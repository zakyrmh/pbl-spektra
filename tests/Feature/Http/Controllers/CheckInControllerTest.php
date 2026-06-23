<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Queue;
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
}
