<?php
/**
 * Pruebas de autenticación de clientes (registro, login, logout, recuperación de contraseña).
 *
 * NOTA: Los usuarios admin y profesional se gestionan desde Orchid, no desde factories en tests.
 * Estas pruebas sólo cubren el flujo de clientes (customer) gestionados desde Laravel.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register_successfully()
    {
        $response = $this->post('/customer/register', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(302); // Redirige después de registro
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_user_can_login_successfully()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/customer/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302); // Redirige después de login
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout_successfully()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/customer/logout');
        $response->assertStatus(302); // Redirige después de logout
        $this->assertGuest();
    }

    public function test_user_can_request_password_reset()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'reset@example.com',
        ]);

        $response = $this->post('/customer/password/email', [
            'email' => 'reset@example.com',
        ]);

        $response->assertStatus(302); // Redirige después de solicitar reset
        // $this->assertDatabaseHas('password_reset_tokens', ['email' => 'reset@example.com']);
    }
}
