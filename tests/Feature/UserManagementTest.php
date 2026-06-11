<?php

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Counter;
use App\Models\Department;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest or non-super-admin cannot access user management', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    // Guest redirect
    $response = test()->get(route('users.index'));
    $response->assertRedirect(route('login'));

    // Visitor forbidden
    $response = test()->actingAs($visitor)->get(route('users.index'));
    $response->assertStatus(403);
});

test('super admin can view user management list', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    User::factory()->create([
        'name' => 'Warga Sawahlunto',
        'email' => 'warga@sawahlunto.go.id',
    ]);

    $response = test()->actingAs($admin)->get(route('users.index'));

    $response->assertStatus(200);
    $response->assertSee('Warga Sawahlunto');
    $response->assertSee('warga@sawahlunto.go.id');
});

test('super admin can store a new user and map no_telp to phone_number', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $response = test()->actingAs($admin)->post(route('users.store'), [
        'name' => 'Petugas Baru',
        'email' => 'petugas@sawahlunto.go.id',
        'nik' => '1234567890123456',
        'no_telp' => '08123456789',
        'role' => 'admin_fo',
        'password' => 'SecurePass123!',
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');

    test()->assertDatabaseHas('users', [
        'name' => 'Petugas Baru',
        'email' => 'petugas@sawahlunto.go.id',
        'nik' => '1234567890123456',
        'no_telp' => '08123456789',
        'role' => 'admin_fo',
    ]);
});

test('super admin can update user profile and no_telp maps to phone_number', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $user */
    $user = User::factory()->create([
        'name' => 'Lama',
        'email' => 'lama@gmail.com',
        'phone_number' => '08000',
        'role' => 'pengunjung',
    ]);

    $response = test()->actingAs($admin)->put(route('users.update', $user->id), [
        'name' => 'Baru',
        'email' => 'baru@gmail.com',
        'no_telp' => '08999',
        'role' => 'pengunjung',
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');

    $user->refresh();
    test()->assertEquals('Baru', $user->name);
    test()->assertEquals('baru@gmail.com', $user->email);
    test()->assertEquals('08999', $user->phone_number);
});

test('super admin cannot delete themselves or other super admins', function () {
    /** @var User $admin1 */
    $admin1 = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $admin2 */
    $admin2 = User::factory()->create([
        'role' => 'super_admin',
    ]);

    // Cannot delete self
    $response = test()->actingAs($admin1)->delete(route('users.destroy', $admin1->id));
    $response->assertStatus(403);

    // Cannot delete another super admin
    $response = test()->actingAs($admin1)->delete(route('users.destroy', $admin2->id));
    $response->assertStatus(403);

    test()->assertDatabaseHas('users', ['id' => $admin1->id]);
    test()->assertDatabaseHas('users', ['id' => $admin2->id]);
});

test('super admin cannot delete user with active booking', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 1']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'KTP']);

    Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Pending',
        'booking_code' => 'TEST-ACTIVE-BOOKING',
    ]);

    $response = test()->actingAs($admin)->delete(route('users.destroy', $visitor->id));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('error', 'Gagal! Akun sedang aktif di antrean atau memiliki booking aktif.');
    test()->assertDatabaseHas('users', ['id' => $visitor->id]);
});

test('super admin cannot delete user with active queue', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $dept = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create(['department_id' => $dept->id, 'name' => 'Loket 1']);
    $service = Service::create(['department_id' => $dept->id, 'name' => 'KTP']);

    $booking = Booking::create([
        'user_id' => $visitor->id,
        'service_id' => $service->id,
        'counter_id' => $counter->id,
        'booking_date' => now()->toDateString(),
        'status' => 'Checked-In',
        'booking_code' => 'TEST-ACTIVE-QUEUE',
    ]);

    Queue::create([
        'booking_id' => $booking->id,
        'counter_id' => $counter->id,
        'service_id' => $service->id,
        'queue_number' => 'DDK-001',
        'status' => 'Waiting',
        'queue_date' => now()->toDateString(),
    ]);

    $response = test()->actingAs($admin)->delete(route('users.destroy', $visitor->id));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('error', 'Gagal! Akun sedang aktif di antrean atau memiliki booking aktif.');
    test()->assertDatabaseHas('users', ['id' => $visitor->id]);
});

test('super admin can delete user if no active booking or queue', function () {
    /** @var User $admin */
    $admin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    /** @var User $visitor */
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $response = test()->actingAs($admin)->delete(route('users.destroy', $visitor->id));

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');
    test()->assertSoftDeleted('users', ['id' => $visitor->id]);
});

test('guest or non-super-admin cannot toggle status, reset password, or view activity log', function () {
    $visitor = User::factory()->create(['role' => 'pengunjung']);
    $target = User::factory()->create(['role' => 'admin_fo']);

    // Guest redirects
    $this->patch(route('users.toggle-status', $target->id))->assertRedirect(route('login'));
    $this->patch(route('users.reset-password', $target->id))->assertRedirect(route('login'));
    $this->get(route('users.activity-log', $target->id))->assertRedirect(route('login'));

    // Non-super-admin 403
    $this->actingAs($visitor)->patch(route('users.toggle-status', $target->id))->assertStatus(403);
    $this->actingAs($visitor)->patch(route('users.reset-password', $target->id))->assertStatus(403);
    $this->actingAs($visitor)->get(route('users.activity-log', $target->id))->assertStatus(403);
});

test('super admin cannot toggle their own status', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($admin)->patch(route('users.toggle-status', $admin->id));
    $response->assertStatus(403);
});

test('super admin can toggle other user status', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $target = User::factory()->create(['role' => 'admin_fo', 'is_active' => true]);

    // Deactivate
    $response = $this->actingAs($admin)->patch(route('users.toggle-status', $target->id));
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $target->refresh();
    expect($target->is_active)->toBeFalse();

    // Assert Audit Log for deactivation
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $admin->id,
        'subject_id' => $target->id,
        'subject_type' => User::class,
        'event' => 'status_toggled',
        'description' => "Akun '{$target->name}' ({$target->email}) berhasil dinonaktifkan.",
    ]);

    // Reactivate
    $this->actingAs($admin)->patch(route('users.toggle-status', $target->id));
    $target->refresh();
    expect($target->is_active)->toBeTrue();

    // Assert Audit Log for activation
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $admin->id,
        'subject_id' => $target->id,
        'subject_type' => User::class,
        'event' => 'status_toggled',
        'description' => "Akun '{$target->name}' ({$target->email}) berhasil diaktifkan.",
    ]);
});

test('super admin cannot reset their own password or another super admin password', function () {
    $admin1 = User::factory()->create(['role' => 'super_admin']);
    $admin2 = User::factory()->create(['role' => 'super_admin']);

    // Cannot reset self password via this endpoint
    $this->actingAs($admin1)->patch(route('users.reset-password', $admin1->id))->assertStatus(403);

    // Cannot reset other super admin password
    $this->actingAs($admin1)->patch(route('users.reset-password', $admin2->id))->assertStatus(403);
});

test('super admin can reset other user password and get a temporary password', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $target = User::factory()->create([
        'role' => 'admin_fo',
        'password' => Hash::make('OldSecurePassword123!'),
    ]);

    $response = $this->actingAs($admin)->patch(route('users.reset-password', $target->id));

    $response->assertRedirect();
    $response->assertSessionHas('temp_password');

    $tempPasswordData = session('temp_password');
    expect($tempPasswordData['user'])->toBe($target->name);
    expect($tempPasswordData['password'])->toHaveLength(12);

    // Assert password updated in database
    $target->refresh();
    expect(Hash::check($tempPasswordData['password'], $target->password))->toBeTrue();

    // Assert Audit Log
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $admin->id,
        'subject_id' => $target->id,
        'subject_type' => User::class,
        'event' => 'password_reset',
        'description' => "Password akun '{$target->name}' ({$target->email}) berhasil direset oleh admin.",
    ]);
});

test('super admin can view user activity log', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $target = User::factory()->create(['role' => 'admin_fo']);

    // Log action BY the target user
    ActivityLog::create([
        'causer_id' => $target->id,
        'event' => 'login',
        'description' => "Pengguna '{$target->name}' berhasil masuk ke sistem.",
    ]);

    // Log action ON the target user by the admin
    ActivityLog::create([
        'causer_id' => $admin->id,
        'subject_id' => $target->id,
        'subject_type' => User::class,
        'event' => 'user_updated',
        'description' => "Data pengguna '{$target->name}' berhasil diperbarui.",
    ]);

    $response = $this->actingAs($admin)->get(route('users.activity-log', $target->id));

    $response->assertStatus(200);
    $response->assertViewIs('super_admin.users.activity_log');
    $response->assertViewHas('user');
    $response->assertViewHas('logs');

    $logs = $response->viewData('logs');
    expect($logs->total())->toBe(2);
});
