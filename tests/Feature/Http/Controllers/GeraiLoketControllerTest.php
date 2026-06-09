<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Setup Super Admin
    $this->superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    // Setup unauthorized user
    $this->unauthorizedUser = User::factory()->create([
        'role' => 'admin_fo',
    ]);
});

// ── Access Checks ────────────────────────────────────────────────────────────

test('guest and non-super-admin are blocked from Gerai & Loket configuration routes', function () {
    // Guest redirection
    $this->get(route('config.index'))->assertRedirect(route('login'));
    $this->post(route('config.departments.store'), [])->assertRedirect(route('login'));

    // Non-super-admin 403
    $this->actingAs($this->unauthorizedUser)->get(route('config.index'))->assertStatus(403);
    $this->actingAs($this->unauthorizedUser)->post(route('config.departments.store'), [])->assertStatus(403);
});

test('super admin can view Gerai & Loket configuration dashboard index', function () {
    $department = Department::create(['name' => 'Dispenduk', 'inisial' => 'DPK']);
    $service = Service::create(['department_id' => $department->id, 'name' => 'KTP']);
    $counter = Counter::create(['department_id' => $department->id, 'name' => 'Loket 1', 'status' => 'aktif']);
    $officer = User::factory()->create(['role' => 'admin_gerai']);

    $response = $this->actingAs($this->superAdmin)->get(route('config.index'));

    $response->assertStatus(200);
    $response->assertViewIs('super_admin.gerai.index');
    $response->assertViewHasAll(['totalDepartments', 'totalStaff', 'departments', 'counters', 'services', 'officers']);

    $response->assertSee('Dispenduk');
    $response->assertSee('DPK');
});

// ── Department CRUD ──────────────────────────────────────────────────────────

test('super admin can create a department without a logo', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('config.departments.store'), [
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'description' => 'Pelayanan kesehatan umum',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));
    $response->assertSessionHas('success', 'Gerai Dinas Kesehatan berhasil dibuat.');

    $this->assertDatabaseHas('departments', [
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'description' => 'Pelayanan kesehatan umum',
        'logo' => null,
    ]);

    // Assert Audit Trail logged
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'department_created',
        'description' => "Gerai baru 'Dinas Kesehatan' (Inisial: DKS) berhasil dibuat.",
    ]);
});

test('super admin can create a department with a logo', function () {
    Storage::fake('public');

    $logo = UploadedFile::fake()->image('logo.jpg', 100, 100);

    $response = $this->actingAs($this->superAdmin)->post(route('config.departments.store'), [
        'name' => 'Dinas Sosial',
        'inisial' => 'DSS',
        'logo' => $logo,
        'description' => 'Pelayanan bantuan sosial',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));

    $department = Department::where('inisial', 'DSS')->first();
    expect($department)->not->toBeNull();
    expect($department->logo)->not->toBeNull();
    expect($department->logo)->toEndWith('.webp');

    // Assert file exists on public fake storage
    Storage::disk('public')->assertExists($department->logo);
});

test('department creation fails on validation errors', function () {
    Department::create(['name' => 'Existing', 'inisial' => 'EXT']);

    $response = $this->actingAs($this->superAdmin)
        ->from(route('config.index'))
        ->post(route('config.departments.store'), [
            'name' => '', // required
            'inisial' => 'EXT', // unique validation fail
        ]);

    $response->assertRedirect(route('config.index'));
    $response->assertSessionHasErrors(['name', 'inisial']);
});

test('super admin can update a department and replace its logo', function () {
    Storage::fake('public');

    // Create a department with an initial logo
    $oldLogoPath = 'logos/old_logo.webp';
    Storage::disk('public')->put($oldLogoPath, 'dummy-content');

    $department = Department::create([
        'name' => 'Dinas Perhubungan Lama',
        'inisial' => 'DPH',
        'logo' => $oldLogoPath,
        'description' => 'Deskripsi lama',
    ]);

    $newLogo = UploadedFile::fake()->image('new_logo.png', 100, 100);

    $response = $this->actingAs($this->superAdmin)->put(route('config.departments.update', $department->id), [
        'name' => 'Dinas Perhubungan Baru',
        'inisial' => 'DPHN',
        'logo' => $newLogo,
        'description' => 'Deskripsi baru',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));

    $department->refresh();
    expect($department->name)->toBe('Dinas Perhubungan Baru');
    expect($department->inisial)->toBe('DPHN');
    expect($department->logo)->toEndWith('.webp');

    // Old logo should be deleted
    Storage::disk('public')->assertMissing($oldLogoPath);
    // New logo should be stored
    Storage::disk('public')->assertExists($department->logo);

    // Assert Audit Trail logged
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'department_updated',
        'description' => "Data Gerai 'Dinas Perhubungan Baru' berhasil diperbarui.",
    ]);
});

test('super admin can delete a department and its logo', function () {
    Storage::fake('public');

    $logoPath = 'logos/to_be_deleted.webp';
    Storage::disk('public')->put($logoPath, 'dummy-content');

    $department = Department::create([
        'name' => 'Dinas Pekerjaan Umum',
        'inisial' => 'DPU',
        'logo' => $logoPath,
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('config.departments.destroy', $department->id));

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));

    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    Storage::disk('public')->assertMissing($logoPath);

    // Assert Audit Trail logged
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'department_deleted',
        'description' => "Gerai 'Dinas Pekerjaan Umum' (Inisial: DPU) dihapus dari sistem.",
    ]);
});

// ── Counter CRUD ─────────────────────────────────────────────────────────────

test('super admin can create a counter and assign services and officers', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $service1 = Service::create(['department_id' => $department->id, 'name' => 'A']);
    $service2 = Service::create(['department_id' => $department->id, 'name' => 'B']);
    $officer = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($this->superAdmin)->post(route('config.counters.store'), [
        'department_id' => $department->id,
        'name' => 'Loket A',
        'location' => 'Lantai 1',
        'status' => 'aktif',
        'officer_id' => $officer->id,
        'services' => [$service1->id, $service2->id],
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'loket']));

    $counter = Counter::where('name', 'Loket A')->first();
    expect($counter)->not->toBeNull();
    expect($counter->status)->toBe('aktif');

    // Services synced
    expect($counter->services)->toHaveCount(2);

    // Officer assigned (departments_id updated to matching department)
    $officer->refresh();
    expect($officer->departments_id)->toBe($department->id);

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'counter_created',
        'description' => "Loket baru 'Loket A' untuk gerai 'Disdukcapil' berhasil dibuat.",
    ]);
});

test('super admin can update a counter, sync new services and reassign officer', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);

    $counter = Counter::create([
        'department_id' => $department->id,
        'name' => 'Loket Lama',
        'status' => 'aktif',
    ]);

    $service = Service::create(['department_id' => $department->id, 'name' => 'A']);
    $counter->services()->sync([$service->id]);

    // Current officer
    $oldOfficer = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $department->id,
    ]);

    // New officer
    $newOfficer = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => null,
    ]);

    $response = $this->actingAs($this->superAdmin)->put(route('config.counters.update', $counter->id), [
        'department_id' => $department->id,
        'name' => 'Loket Baru',
        'location' => 'Lantai 2',
        'status' => 'istirahat',
        'officer_id' => $newOfficer->id,
        'services' => [], // clear services
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'loket']));

    $counter->refresh();
    expect($counter->name)->toBe('Loket Baru');
    expect($counter->status)->toBe('istirahat');
    expect($counter->services)->toHaveCount(0);

    // Old officer reset
    $oldOfficer->refresh();
    expect($oldOfficer->departments_id)->toBeNull();

    // New officer set
    $newOfficer->refresh();
    expect($newOfficer->departments_id)->toBe($department->id);

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'counter_updated',
        'description' => "Data Loket 'Loket Baru' berhasil diperbarui.",
    ]);
});

test('super admin can delete a counter', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create([
        'department_id' => $department->id,
        'name' => 'Loket DDK',
        'status' => 'aktif',
    ]);

    $officer = User::factory()->create([
        'role' => 'admin_gerai',
        'departments_id' => $department->id,
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('config.counters.destroy', $counter->id));

    $response->assertRedirect(route('config.index', ['tab' => 'loket']));

    $this->assertDatabaseMissing('counters', ['id' => $counter->id]);

    // Officer department_id is reset to null
    $officer->refresh();
    expect($officer->departments_id)->toBeNull();

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'counter_deleted',
        'description' => "Loket 'Loket DDK' berhasil dihapus dari sistem.",
    ]);
});

test('super admin can toggle counter status', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $counter = Counter::create([
        'department_id' => $department->id,
        'name' => 'Loket A',
        'status' => 'aktif',
    ]);

    $response = $this->actingAs($this->superAdmin)->patch(route('config.counters.toggle-status', $counter->id), [
        'status' => 'istirahat',
    ]);

    $response->assertRedirect();
    $counter->refresh();
    expect($counter->status)->toBe('istirahat');

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'counter_status_toggled',
        'description' => "Status loket 'Loket A' diubah dari 'aktif' menjadi 'istirahat'.",
    ]);
});

// ── Service CRUD ─────────────────────────────────────────────────────────────

test('super admin can create a service', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);

    $response = $this->actingAs($this->superAdmin)->post(route('config.services.store'), [
        'department_id' => $department->id,
        'name' => 'Perekaman KTP-el',
        'description' => 'Proses rekam foto & iris mata',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'layanan']));

    $this->assertDatabaseHas('services', [
        'department_id' => $department->id,
        'name' => 'Perekaman KTP-el',
        'description' => 'Proses rekam foto & iris mata',
    ]);

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'service_created',
        'description' => "Layanan baru 'Perekaman KTP-el' untuk gerai 'Disdukcapil' berhasil ditambahkan.",
    ]);
});

test('super admin can update a service', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $service = Service::create([
        'department_id' => $department->id,
        'name' => 'Layanan Lama',
    ]);

    $response = $this->actingAs($this->superAdmin)->put(route('config.services.update', $service->id), [
        'department_id' => $department->id,
        'name' => 'Layanan Baru',
        'description' => 'Deskripsi Baru',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'layanan']));

    $service->refresh();
    expect($service->name)->toBe('Layanan Baru');
    expect($service->description)->toBe('Deskripsi Baru');

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'service_updated',
        'description' => "Data Layanan 'Layanan Baru' berhasil diperbarui.",
    ]);
});

test('super admin can delete a service', function () {
    $department = Department::create(['name' => 'Disdukcapil', 'inisial' => 'DDK']);
    $service = Service::create([
        'department_id' => $department->id,
        'name' => 'Layanan Hapus',
    ]);

    $response = $this->actingAs($this->superAdmin)->delete(route('config.services.destroy', $service->id));

    $response->assertRedirect(route('config.index', ['tab' => 'layanan']));

    $this->assertDatabaseMissing('services', ['id' => $service->id]);

    // Assert Audit Trail
    $this->assertDatabaseHas('activity_logs', [
        'causer_id' => $this->superAdmin->id,
        'event' => 'service_deleted',
        'description' => "Layanan 'Layanan Hapus' berhasil dihapus dari sistem.",
    ]);
});
