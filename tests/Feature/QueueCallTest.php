<?php

use App\Models\Counter;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access queue call page and is redirected to login', function () {
    $response = $this->get(route('admin.fo.call'));
    $response->assertRedirect(route('login'));
});

test('visitor role is forbidden from accessing queue call page', function () {
    $visitor = User::factory()->create(['role' => 'pengunjung']);

    $response = $this->actingAs($visitor)->get(route('admin.fo.call'));
    $response->assertStatus(403);
});

test('front office admin can access queue call page', function () {
    $fo = User::factory()->create(['role' => 'admin_fo']);

    $dept = Department::create([
        'name' => 'Front Office',
        'inisial' => 'FO',
        'description' => 'Loket Front Office Terdepan',
    ]);

    Counter::create([
        'department_id' => $dept->id,
        'name' => 'Loket FO 1',
        'location' => 'Lantai 1 Depan',
    ]);

    $response = $this->actingAs($fo)->get(route('admin.fo.call'));
    $response->assertStatus(200);
    $response->assertSee('Operasional Panggilan Antrean');
});
