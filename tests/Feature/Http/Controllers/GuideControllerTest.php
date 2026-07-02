<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuideControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_guide_page(): void
    {
        $response = $this->get(route('customer.guide'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_guide_page(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)->get(route('customer.guide'));

        $response->assertStatus(200);
        $response->assertViewIs('guide.index');
        $response->assertSee('Disdukcapil');
        $response->assertSee('Bank Nagari');
    }
}
