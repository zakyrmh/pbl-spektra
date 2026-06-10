<?php

use App\Events\QueueCreated;
use App\Mail\BookingCancelledMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup Admin FO
    $this->foAdmin = User::factory()->create([
        'role' => 'admin_fo',
    ]);

    // Setup Guest/Visitor with a valid NIK by default
    $this->visitorUser = User::factory()->create([
        'role' => 'pengunjung',
        'nik' => '1234567890123456',
    ]);

    // Setup default Department and Service
    $this->defaultDepartment = Department::create([
        'name' => 'Disdukcapil Default',
        'inisial' => 'DDK',
    ]);

    $this->defaultService = Service::create([
        'department_id' => $this->defaultDepartment->id,
        'name' => 'Cetak KTP Default',
    ]);
});

// ── Access Protection ────────────────────────────────────────────────────────

test('guest or non-fo-admin cannot access check-in routes', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-123',
    ]);

    // Guest redirection
    $this->get(route('admin.fo.checkin'))->assertRedirect(route('login'));
    $this->post(route('admin.fo.checkin.verify'), [])->assertRedirect(route('login'));
    $this->post(route('admin.fo.checkin.approve', $booking->id), [])->assertRedirect(route('login'));

    // Non-fo-admin 403
    $this->actingAs($this->visitorUser)->get(route('admin.fo.checkin'))->assertStatus(403);
    $this->actingAs($this->visitorUser)->post(route('admin.fo.checkin.verify'), [])->assertStatus(403);
});

// ── GET Index ────────────────────────────────────────────────────────────────

test('fo admin can access check-in page', function () {
    $response = $this->actingAs($this->foAdmin)->get(route('admin.fo.checkin'));
    $response->assertStatus(200);
    $response->assertViewIs('admin.fo.checkin');
});

// ── POST Verify ──────────────────────────────────────────────────────────────

test('verification fails when booking code is not found', function () {
    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.checkin'))
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'NON-EXISTENT',
        ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('error');
    $response->assertSessionHas('searched_code', 'NON-EXISTENT');
});

test('verification fails when booking is not in Pending status', function (string $status) {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => $status,
        'booking_code' => 'B-STATUS',
    ]);

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.checkin'))
        ->post(route('admin.fo.checkin.verify'), [
            'booking_code' => 'B-STATUS',
        ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('warning');
    $response->assertSessionHas('booking');
})->with([
    'Checked-In',
    'Completed',
    'Cancelled',
]);

test('verification shows NIK input form if visitor NIK is empty', function () {
    $this->visitorUser->update(['nik' => null]);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-NO-NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.verify'), [
        'booking_code' => 'B-NO-NIK',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('admin.fo.checkin');
    $response->assertViewHas('booking');
    $response->assertViewHas('nik_required', true);
});

test('verification fails if inputted NIK is already registered to another user', function () {
    $this->visitorUser->update(['nik' => null]);
    $otherUser = User::factory()->create(['nik' => '1234567890123456']);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-NO-NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.verify'), [
        'booking_code' => 'B-NO-NIK',
        'nik_input' => '1234567890123456',
    ]);

    $response->assertStatus(200);
    $response->assertSessionHas('error', 'NIK <strong>1234567890123456</strong> sudah terdaftar di sistem untuk pengguna lain.');
    $response->assertViewHas('nik_required', true);
});

test('verification updates NIK and continues if inputted NIK is unique', function () {
    $this->visitorUser->update(['nik' => null]);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-NO-NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.verify'), [
        'booking_code' => 'B-NO-NIK',
        'nik_input' => '9999888877776666',
    ]);

    $response->assertStatus(200);
    $response->assertViewHas('nik_required', false);

    // Verify NIK updated
    $this->visitorUser->refresh();
    expect($this->visitorUser->nik)->toBe('9999888877776666');

    // Verify UPDATE_NIK activity logged
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'UPDATE_NIK',
        'model_type' => 'User',
        'model_id' => $this->visitorUser->id,
    ]);
});

test('verification continues normally if NIK is already filled', function () {
    $this->visitorUser->update(['nik' => '1111222233334444']);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-WITH-NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.verify'), [
        'booking_code' => 'B-WITH-NIK',
    ]);

    $response->assertStatus(200);
    $response->assertViewHas('nik_required', false);
});

// ── POST Approve ─────────────────────────────────────────────────────────────

test('approve fails if booking status is not Pending', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Checked-In',
        'booking_code' => 'B-NOT-PENDING',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.approve', $booking->id));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('warning', 'Booking ini tidak dapat diproses.');
});

test('approve fails if user NIK is empty', function () {
    $this->visitorUser->update(['nik' => null]);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-NO-NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.approve', $booking->id));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('error', 'NIK wajib diisi sebelum menyetujui check-in.');
});

test('approve issues a queue ticket successfully', function () {
    Event::fake();

    $counter = Counter::create([
        'department_id' => $this->defaultDepartment->id,
        'name' => 'Loket 1',
        'status' => 'aktif',
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-OK',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.approve', $booking->id));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('checkin_result');

    // Assert booking status updated
    $booking->refresh();
    expect($booking->status)->toBe('Checked-In');
    expect($booking->checked_in_at)->not->toBeNull();

    // Assert queue ticket created
    $this->assertDatabaseHas('queues', [
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $this->defaultService->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
    ]);

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'VERIFY_CHECKIN',
        'model_type' => 'Booking',
        'model_id' => $booking->id,
    ]);

    // Assert Event Dispatched
    Event::assertDispatched(QueueCreated::class);
});

test('approve fails if no counter is available for department', function () {
    // We already have a defaultDepartment and defaultService, but no counter is created for them in this test.

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-FAIL-COUNTER',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.approve', $booking->id));

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('error');
    expect(session('error'))->toContain('Belum ada loket/counter yang terdaftar');
});

// ── POST Reject ──────────────────────────────────────────────────────────────

test('reject fails if booking cannot be checked in', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Cancelled',
        'booking_code' => 'B-CANCELLED',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.reject', $booking->id), [
        'reason' => 'Dokumen tidak lengkap',
    ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('warning', 'Booking ini tidak dapat diproses.');
});

test('reject fails on validation errors for reason', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-REJ-VAL',
    ]);

    $response = $this->actingAs($this->foAdmin)
        ->from(route('admin.fo.checkin'))
        ->post(route('admin.fo.checkin.reject', $booking->id), [
            'reason' => '123', // too short
        ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHasErrors(['reason']);
});

test('reject cancels booking, notifies visitor, and sends email', function () {
    Mail::fake();

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'B-REJECT',
    ]);

    $response = $this->actingAs($this->foAdmin)->post(route('admin.fo.checkin.reject', $booking->id), [
        'reason' => 'Berkas persyaratan fotokopi KK tidak dibawa.',
    ]);

    $response->assertRedirect(route('admin.fo.checkin'));
    $response->assertSessionHas('success');

    // Assert status updated
    $booking->refresh();
    expect($booking->status)->toBe('Cancelled');
    expect($booking->cancel_reason)->toBe('Berkas persyaratan fotokopi KK tidak dibawa.');

    // Assert Notification created
    $this->assertDatabaseHas('notifications', [
        'user_id' => $this->visitorUser->id,
        'title' => 'Booking Ditolak FO',
        'message' => "Reservasi antrean untuk layanan Cetak KTP Default pada {$booking->booking_date->translatedFormat('d F Y')} ditolak oleh petugas Front Office dengan alasan: Berkas persyaratan fotokopi KK tidak dibawa..",
    ]);

    // Assert ActivityLog
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'REJECT_BOOKING',
        'model_type' => 'Booking',
        'model_id' => $booking->id,
    ]);

    // Assert Mail Sent
    Mail::assertSent(BookingCancelledMail::class);
});

// ── GET Verify API ───────────────────────────────────────────────────────────

test('verify api validation fails when code is missing', function () {
    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.bookings.verify'));
    $response->assertStatus(400);
    $response->assertJsonPath('message', 'Booking code is required.');
});

test('verify api fails when booking is not found or not pending', function () {
    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.bookings.verify', ['code' => 'NON-EXISTENT-API']));
    $response->assertStatus(404);
});

test('verify api returns details for valid pending booking', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'API-VERIFY-OK',
    ]);

    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.bookings.verify', ['code' => 'API-VERIFY-OK']));

    $response->assertStatus(200);
    $response->assertJson([
        'id' => $booking->id,
        'booking_code' => 'API-VERIFY-OK',
        'user' => [
            'name' => $this->visitorUser->name,
            'nik' => $this->visitorUser->nik,
        ],
        'department' => [
            'name' => 'Disdukcapil Default',
        ],
        'service' => [
            'name' => 'Cetak KTP Default',
        ],
    ]);
});

// ── POST CheckIn API ─────────────────────────────────────────────────────────

test('checkin api fails if status is not Pending', function () {
    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Cancelled',
        'booking_code' => 'API-CI-FAIL',
    ]);

    $response = $this->actingAs($this->foAdmin)->postJson(route('api.fo.bookings.checkin', $booking->id));

    $response->assertStatus(422);
    $response->assertJsonPath('message', 'Booking status is not Pending.');
});

test('checkin api approves booking and issues ticket', function () {
    Event::fake();

    $counter = Counter::create([
        'department_id' => $this->defaultDepartment->id,
        'name' => 'Loket 1',
        'status' => 'aktif',
    ]);

    $booking = Booking::create([
        'user_id' => $this->visitorUser->id,
        'service_id' => $this->defaultService->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'API-CI-OK',
    ]);

    $response = $this->actingAs($this->foAdmin)->postJson(route('api.fo.bookings.checkin', $booking->id));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
    ]);

    $booking->refresh();
    expect($booking->status)->toBe('Checked-In');

    // Assert Queue created
    $this->assertDatabaseHas('queues', [
        'booking_id' => $booking->id,
        'queue_number' => 'DDK-001',
    ]);

    Event::assertDispatched(QueueCreated::class);
});

// ── POST Walk-In API ─────────────────────────────────────────────────────────

test('walk-in api validation fails on invalid payload', function () {
    $response = $this->actingAs($this->foAdmin)->postJson(route('api.fo.queues.walkin'), [
        'nik' => '123', // digits:16 fail
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['nik', 'name', 'purpose', 'department_id']);
});

test('walk-in api creates visitor and issues queue ticket successfully', function () {
    Event::fake();

    $counter = Counter::create([
        'department_id' => $this->defaultDepartment->id,
        'name' => 'Loket Pendidikan',
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($this->foAdmin)->postJson(route('api.fo.queues.walkin'), [
        'nik' => '1234567890123450',
        'name' => 'Budi Santoso',
        'purpose' => 'Legalisir ijazah SMA',
        'department_id' => $this->defaultDepartment->id,
        'service_name' => 'Cetak KTP Default',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'visitor_name' => 'Budi Santoso',
    ]);

    // Visitor created in database
    $this->assertDatabaseHas('visitors', [
        'nik' => '1234567890123450',
        'name' => 'Budi Santoso',
        'purpose' => 'Legalisir ijazah SMA',
    ]);

    // Queue ticket created in database
    $this->assertDatabaseHas('queues', [
        'booking_id' => null, // walk-in is null
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
    ]);

    // ActivityLog written
    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->foAdmin->id,
        'action' => 'WALKIN_TICKET',
    ]);

    Event::assertDispatched(QueueCreated::class);
});

// ── GET Visitor Check NIK API ────────────────────────────────────────────────

test('check NIK api validation fails on invalid length', function () {
    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.visitors.check-nik', ['nik' => '123']));
    $response->assertStatus(400);
});

test('check NIK api returns found false when visitor does not exist', function () {
    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.visitors.check-nik', ['nik' => '1122334455667788']));
    $response->assertStatus(200);
    $response->assertJson(['found' => false]);
});

test('check NIK api returns found true and details when visitor exists', function () {
    $visitor = Visitor::create([
        'nik' => '1122334455667788',
        'name' => 'Joko Widodo',
        'phone' => '0812345678',
        'purpose' => 'Check NIK',
    ]);

    $response = $this->actingAs($this->foAdmin)->getJson(route('api.fo.visitors.check-nik', ['nik' => '1122334455667788']));
    $response->assertStatus(200);
    $response->assertJson([
        'found' => true,
        'name' => 'Joko Widodo',
        'nik' => '1122334455667788',
    ]);
});
