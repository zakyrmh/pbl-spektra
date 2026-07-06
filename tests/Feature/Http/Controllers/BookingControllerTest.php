<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Mail\BookingSuccessMail;
use App\Models\Department;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_booking_index(): void
    {
        $response = $this->get(route('booking.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_access_booking_index_and_view_history(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);
        $department = Department::create([
            'name' => 'Dinas Kesehatan',
            'inisial' => 'DK',
            'nomor_loket' => '01',
            'is_open' => true,
        ]);

        Queue::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'booking_code' => 'BK-TEST-123456',
            'booking_date' => now()->toDateString(),
            'status' => 'Booked',
            'purpose' => 'Konsultasi kesehatan',
            'queue_number' => 'DK-001',
            'session_name' => 'Online',
        ]);

        $response = $this->actingAs($user)->get(route('booking.index'));

        $response->assertStatus(200);
        $response->assertSee('BK-TEST-123456');
        $response->assertSee('Dinas Kesehatan');
    }

    public function test_user_can_access_booking_create_form(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);
        $department = Department::create([
            'name' => 'Dinas Pendidikan',
            'inisial' => 'DP',
            'nomor_loket' => '02',
            'is_open' => true,
        ]);

        $response = $this->actingAs($user)->get(route('booking.create'));

        $response->assertStatus(200);
        $response->assertSee('Dinas Pendidikan');
    }

    public function test_validation_fails_for_store_booking(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)->post(route('booking.store'), [
            'department_id' => '',
            'keperluan' => 'abc', // too short
            'booking_date' => now()->subDay()->toDateString(), // yesterday
            'session_name' => 'Invalid Session',
        ]);

        $response->assertSessionHasErrors(['department_id', 'keperluan', 'booking_date', 'session_name']);
    }

    public function test_user_can_successfully_store_booking(): void
    {
        Mail::fake();

        $user = User::factory()->create(['role' => UserRole::Pengunjung]);
        $department = Department::create([
            'name' => 'Dinas Kesehatan',
            'inisial' => 'DK',
            'nomor_loket' => '01',
            'is_open' => true,
        ]);

        $response = $this->actingAs($user)->post(route('booking.store'), [
            'department_id' => $department->id,
            'keperluan' => 'Pengurusan izin praktik apoteker',
            'booking_date' => now()->addDay()->toDateString(),
            'session_name' => 'Sesi 1',
        ]);

        $booking = Queue::first();
        $this->assertNotNull($booking);

        $response->assertRedirect(route('booking.show', $booking));
        $this->assertEquals($user->id, $booking->user_id);
        $this->assertEquals($department->id, $booking->department_id);
        $this->assertEquals(QueueStatus::Booked, $booking->status);
        $this->assertEquals('Sesi 1', $booking->session_name);
        $this->assertStringStartsWith('BK-DK-', $booking->booking_code);

        Mail::assertQueued(BookingSuccessMail::class, function ($mail) use ($user, $booking) {
            return $mail->hasTo($user->email) && $mail->booking->id === $booking->id;
        });
    }

    public function test_policy_restricts_unauthorized_booking_view(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Pengunjung]);
        $otherUser = User::factory()->create(['role' => UserRole::Pengunjung]);
        $department = Department::create([
            'name' => 'Dinas Kesehatan',
            'inisial' => 'DK',
            'nomor_loket' => '01',
            'is_open' => true,
        ]);

        $booking = Queue::create([
            'user_id' => $owner->id,
            'department_id' => $department->id,
            'booking_code' => 'BK-SECRET-999',
            'booking_date' => now()->toDateString(),
            'purpose' => 'Rahasia',
            'status' => 'Booked',
            'queue_number' => 'DK-001',
            'session_name' => 'Online',
        ]);

        // Owner can view
        $response = $this->actingAs($owner)->get(route('booking.show', $booking));
        $response->assertStatus(200);

        // Other pengunjung cannot view
        $response = $this->actingAs($otherUser)->get(route('booking.show', $booking));
        $response->assertStatus(403);

        // Admin FO can view
        $adminFo = User::factory()->create(['role' => UserRole::AdminFo]);
        $response = $this->actingAs($adminFo)->get(route('booking.show', $booking));
        $response->assertStatus(200);

        // Super Admin can view
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $response = $this->actingAs($superAdmin)->get(route('booking.show', $booking));
        $response->assertStatus(200);
    }
}
