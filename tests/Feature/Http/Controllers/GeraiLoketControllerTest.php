<?php

use App\Models\Department;
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
    $department = Department::create([
        'name' => 'Dispenduk',
        'inisial' => 'DPK',
        'nomor_loket' => '01',
    ]);
    $officer = User::factory()->create(['role' => 'admin_gerai']);

    $response = $this->actingAs($this->superAdmin)->get(route('config.index'));

    $response->assertStatus(200);
    $response->assertViewIs('super_admin.gerai.index');
    $response->assertViewHasAll(['totalDepartments', 'totalStaff', 'departments', 'officers']);

    $response->assertSee('Dispenduk');
    $response->assertSee('DPK');
});

// ── Department CRUD ──────────────────────────────────────────────────────────

test('super admin can create a department without a logo', function () {
    $response = $this->actingAs($this->superAdmin)->post(route('config.departments.store'), [
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'nomor_loket' => '02',
        'description' => 'Pelayanan kesehatan umum',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));
    $response->assertSessionHas('success', 'Gerai Dinas Kesehatan berhasil dibuat.');

    $this->assertDatabaseHas('departments', [
        'name' => 'Dinas Kesehatan',
        'inisial' => 'DKS',
        'nomor_loket' => '02',
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
        'nomor_loket' => '03',
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
    Department::create([
        'name' => 'Existing',
        'inisial' => 'EXT',
        'nomor_loket' => '04',
    ]);

    $response = $this->actingAs($this->superAdmin)
        ->from(route('config.index'))
        ->post(route('config.departments.store'), [
            'name' => '', // required
            'inisial' => 'EXT', // unique validation fail
            'nomor_loket' => '', // required
        ]);

    $response->assertRedirect(route('config.index'));
    $response->assertSessionHasErrors(['name', 'inisial', 'nomor_loket']);
});

test('super admin can update a department and replace its logo', function () {
    Storage::fake('public');

    // Create a department with an initial logo
    $oldLogoPath = 'logos/old_logo.webp';
    Storage::disk('public')->put($oldLogoPath, 'dummy-content');

    $department = Department::create([
        'name' => 'Dinas Perhubungan Lama',
        'inisial' => 'DPH',
        'nomor_loket' => '05',
        'logo' => $oldLogoPath,
        'description' => 'Deskripsi lama',
    ]);

    $newLogo = UploadedFile::fake()->image('new_logo.png', 100, 100);

    $response = $this->actingAs($this->superAdmin)->put(route('config.departments.update', $department->id), [
        'name' => 'Dinas Perhubungan Baru',
        'inisial' => 'DPHN',
        'nomor_loket' => '06',
        'logo' => $newLogo,
        'description' => 'Deskripsi baru',
    ]);

    $response->assertRedirect(route('config.index', ['tab' => 'gerai']));

    $department->refresh();
    expect($department->name)->toBe('Dinas Perhubungan Baru');
    expect($department->inisial)->toBe('DPHN');
    expect($department->nomor_loket)->toBe('06');
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
        'nomor_loket' => '07',
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
