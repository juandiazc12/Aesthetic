<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_orchid_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/admin/orchid');
        $response->assertOk();
    }

    public function test_professional_accesses_only_agenda_and_ratings()
    {
        $professional = User::factory()->create(['role' => 'professional']);
        $this->actingAs($professional);
        $responseAgenda = $this->get('/professional/agenda');
        $responseRatings = $this->get('/professional/ratings');
        $responseAdmin = $this->get('/admin/orchid');
        $responseAgenda->assertOk();
        $responseRatings->assertOk();
        $responseAdmin->assertForbidden();
    }

    public function test_customer_cannot_access_restricted_routes()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->get('/admin/orchid');
        $response->assertForbidden();
    }

    public function test_middleware_blocks_unauthorized_routes()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($user);
        $response = $this->get('/admin/orchid');
        $response->assertForbidden();
    }
}
