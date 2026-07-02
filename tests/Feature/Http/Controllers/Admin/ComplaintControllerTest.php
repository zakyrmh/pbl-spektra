<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_complaints(): void
    {
        $response = $this->get(route('admin.complaints.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_super_admin_cannot_access_admin_complaints(): void
    {
        $visitor = User::factory()->create(['role' => UserRole::Pengunjung]);

        $response = $this->actingAs($visitor)->get(route('admin.complaints.index'));

        // Either 403 Forbidden or redirect depending on RoleMiddleware.
        // Let's assert it is 403.
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_complaints_index_with_data(): void
    {
        $admin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $visitor = User::factory()->create(['role' => UserRole::Pengunjung]);

        Complaint::create([
            'user_id' => $visitor->id,
            'subject' => 'Kendala Kursi Loket',
            'category' => 'Fasilitas',
            'content' => 'Kursi di loket Disdukcapil banyak yang rusak.',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.complaints.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.complaints.index');
        $response->assertSee('Kendala Kursi Loket');
        $response->assertSee('Fasilitas');
        $response->assertSee($visitor->name);
    }

    public function test_super_admin_can_update_complaint_status(): void
    {
        $admin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $visitor = User::factory()->create(['role' => UserRole::Pengunjung]);

        $complaint = Complaint::create([
            'user_id' => $visitor->id,
            'subject' => 'Kendala Kursi Loket',
            'category' => 'Fasilitas',
            'content' => 'Kursi di loket Disdukcapil banyak yang rusak.',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.complaints.index'))
            ->put(route('admin.complaints.update', $complaint), [
                'status' => 'Processing',
            ]);

        $response->assertRedirect(route('admin.complaints.index'));
        $response->assertSessionHas('success');

        $this->assertEquals('Processing', $complaint->fresh()->status);
    }

    public function test_update_validation_fails_for_invalid_status(): void
    {
        $admin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $visitor = User::factory()->create(['role' => UserRole::Pengunjung]);

        $complaint = Complaint::create([
            'user_id' => $visitor->id,
            'subject' => 'Kendala Kursi Loket',
            'category' => 'Fasilitas',
            'content' => 'Kursi di loket Disdukcapil banyak yang rusak.',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.complaints.index'))
            ->put(route('admin.complaints.update', $complaint), [
                'status' => 'InvalidStatus',
            ]);

        $response->assertRedirect(route('admin.complaints.index'));
        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('Pending', $complaint->fresh()->status);
    }
}
