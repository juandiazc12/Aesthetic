<?php
/**
 * Pruebas de acceso según rol (cliente, profesional, admin).
 *
 * NOTA: Los roles admin y profesional se asignan desde Orchid. Los clientes se gestionan desde Laravel.
 * Estas pruebas sólo simulan acceso usando usuarios creados por factories, pero en producción los roles se gestionan desde Orchid.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    // Pruebas para:
    // - Validar acceso según rol (cliente, profesional, admin)
    public function test_client_cannot_access_admin_routes()
    {
        $client = \App\Models\User::whereHas('roles', function($q) { $q->where('slug', 'client'); })->first();
        if (!$client) {
            $this->markTestSkipped('No hay clientes disponibles en la base de datos.');
        }
        $this->actingAs($client);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_professional_cannot_access_admin_routes()
    {
        $professional = \App\Models\User::whereHas('roles', function($q) { $q->where('slug', 'professional'); })->first();
        if (!$professional) {
            $this->markTestSkipped('No hay profesionales disponibles en la base de datos.');
        }
        $this->actingAs($professional);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes()
    {
        $admin = \App\Models\User::whereHas('roles', function($q) { $q->where('slug', 'admin'); })->first();
        if (!$admin) {
            $this->markTestSkipped('No hay administradores disponibles en la base de datos.');
        }
        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
