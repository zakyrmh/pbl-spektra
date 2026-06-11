<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access queue monitor page and is redirected to login', function () {
    $response = test()->get(route('admin.fo.monitor'));
    $response->assertRedirect(route('login'));
});

test('visitor role is forbidden from accessing queue monitor page', function () {
    /** @var User $visitor */
    $visitor = User::factory()->create(['role' => 'pengunjung']);

    $response = test()->actingAs($visitor)->get(route('admin.fo.monitor'));
    $response->assertStatus(403);
});

test('front office admin can access queue monitor page', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    // Seed at least one department so the page can render table headers and body
    Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Dinas Kependudukan dan Catatan Sipil',
    ]);

    $response = test()->actingAs($fo)->get(route('admin.fo.monitor'));
    $response->assertStatus(200);
    $response->assertSee('Monitor Kepadatan Antrean');
    $response->assertSee('Disdukcapil');
    $response->assertSee('DDK');
});

test('front office admin can poll queue monitor data via json', function () {
    /** @var User $fo */
    $fo = User::factory()->create(['role' => 'admin_fo']);

    Department::create([
        'name' => 'Disdukcapil',
        'inisial' => 'DDK',
        'description' => 'Dinas Kependudukan dan Catatan Sipil',
    ]);

    $response = test()->actingAs($fo)->get(route('admin.fo.monitor', ['json' => 'true']), [
        'Accept' => 'application/json',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'metrics' => [
            'total_waiting',
            'total_serving',
            'average_wait_time',
        ],
        'departments' => [
            '*' => [
                'id',
                'name',
                'inisial',
                'description',
                'waiting_count',
                'serving_count',
                'density',
                'density_class',
                'density_dot',
            ],
        ],
    ]);
});
