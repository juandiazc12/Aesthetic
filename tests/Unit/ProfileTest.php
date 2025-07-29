<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile_fields_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->put('/profile', [
            'name' => 'Nuevo Nombre',
            'phone' => '3001234567',
            'email' => 'nuevo@example.com',
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
            'phone' => '3001234567',
            'email' => 'nuevo@example.com',
        ]);
    }

    public function test_cannot_update_with_existing_email()
    {
        User::factory()->create(['email' => 'used@example.com']);
        $user = User::factory()->create(['email' => 'original@example.com']);
        $this->actingAs($user);
        $response = $this->put('/profile', [
            'name' => 'Nombre',
            'phone' => '3001234567',
            'email' => 'used@example.com',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_only_owner_can_update_profile()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($other);
        $response = $this->put("/profile/{$user->id}", [
            'name' => 'Hack',
            'phone' => '3000000000',
            'email' => 'hack@example.com',
        ]);
        $response->assertForbidden();
    }
}
