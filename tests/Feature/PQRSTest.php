<?php
/**
 * Pruebas de envío de PQRS por parte de clientes.
 *
 * NOTA: Sólo clientes pueden enviar PQRS desde Laravel.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PQRSTest extends TestCase
{
    use RefreshDatabase;

    // Pruebas para:
    // - Enviar formulario de PQRS
    public function test_user_can_submit_pqrs()
    {
        $user = \App\Models\User::factory()->create(); // Usar factories solo para clientes
        $this->actingAs($user);
        $response = $this->post(route('pqrs.store'), [ // Verificar que la ruta exista
            'subject' => 'Consulta',
            'message' => 'Quiero más información sobre el servicio.',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('pqrs', [ // Verificar que la tabla exista
            'user_id' => $user->id,
            'subject' => 'Consulta',
        ]);
    }
}
