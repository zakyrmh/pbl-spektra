<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpCenterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_help_center(): void
    {
        $response = $this->get(route('customer.help'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_post_complaint(): void
    {
        $response = $this->post(route('customer.help.store'), [
            'subject' => 'Kendala Scan QR',
            'category' => 'Sistem/Teknis',
            'content' => 'Detail aduan kendala aplikasi.',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_help_center(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)->get(route('customer.help'));

        $response->assertStatus(200);
        $response->assertViewIs('help.index');
        $response->assertSee('Pusat Bantuan');
        $response->assertSee('Formulir Pengaduan Warga');
    }

    public function test_complaint_validation_fails_for_empty_fields(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)
            ->from(route('customer.help'))
            ->post(route('customer.help.store'), [
                'subject' => '',
                'category' => '',
                'content' => '',
            ]);

        $response->assertRedirect(route('customer.help'));
        $response->assertSessionHasErrors(['subject', 'category', 'content']);
    }

    public function test_complaint_validation_fails_for_invalid_category_or_short_content(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)
            ->from(route('customer.help'))
            ->post(route('customer.help.store'), [
                'subject' => 'Kendala Scan QR',
                'category' => 'InvalidCategory',
                'content' => 'Short',
            ]);

        $response->assertRedirect(route('customer.help'));
        $response->assertSessionHasErrors(['category', 'content']);
    }

    public function test_authenticated_user_can_successfully_submit_complaint(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($user)
            ->from(route('customer.help'))
            ->post(route('customer.help.store'), [
                'subject' => 'Kendala Scan QR',
                'category' => 'Sistem/Teknis',
                'content' => 'Saya menghadapi kendala ketika melakukan scan QR Code antrean di gerai FO.',
            ]);

        $response->assertRedirect(route('customer.help'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('complaints', [
            'user_id' => $user->id,
            'subject' => 'Kendala Scan QR',
            'category' => 'Sistem/Teknis',
            'content' => 'Saya menghadapi kendala ketika melakukan scan QR Code antrean di gerai FO.',
        ]);
    }
}
