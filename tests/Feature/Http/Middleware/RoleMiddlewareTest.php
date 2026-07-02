<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Define a temporary route protected by RoleMiddleware to test its behavior
    Route::middleware('role:admin_fo,admin_gerai')->get('/_test/role-middleware', function () {
        return 'success';
    });
});

test('unauthenticated visitor is redirected to login page when accessing role-protected route', function () {
    $response = $this->get('/_test/role-middleware');

    $response->assertRedirect(route('login'));
});

test('super admin bypasses all role middleware checks and is allowed access', function () {
    $superAdmin = User::factory()->create([
        'role' => 'super_admin',
    ]);

    $response = $this->actingAs($superAdmin)->get('/_test/role-middleware');

    $response->assertStatus(200);
    $response->assertSee('success');
});

test('user with allowed role is granted access', function (string $role) {
    $user = User::factory()->create([
        'role' => $role,
    ]);

    $response = $this->actingAs($user)->get('/_test/role-middleware');

    $response->assertStatus(200);
    $response->assertSee('success');
})->with([
    'admin_fo',
    'admin_gerai',
]);

test('user with unauthorized role is forbidden with 403 status code', function () {
    $visitor = User::factory()->create([
        'role' => 'pengunjung',
    ]);

    $response = $this->actingAs($visitor)->get('/_test/role-middleware');

    $response->assertStatus(403);
    $response->assertSee('Anda tidak memiliki akses ke halaman ini.');
});
