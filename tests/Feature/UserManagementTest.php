<?php

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
