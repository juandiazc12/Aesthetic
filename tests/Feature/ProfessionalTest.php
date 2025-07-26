<?php
/**
 * Pruebas de interacción de clientes con profesionales y servicios: calificar, ver y filtrar servicios.
 *
 * NOTA: Los servicios y profesionales se crean y gestionan desde Orchid.
 * Estas pruebas sólo validan la visualización y calificación por parte de clientes.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfessionalTest extends TestCase
{
    use RefreshDatabase;

    // Pruebas para:
    // - Calificar a un profesional
    // - Ver servicios disponibles
    // - Filtrar servicios por profesional
    public function test_user_can_rate_professional()
    {
        $user = \App\Models\User::factory()->create();
        $professional = \App\Models\User::whereHas('roles', function($q) { $q->where('slug', 'professional'); })->first();
        if (!$professional) {
            $this->markTestSkipped('No hay profesionales disponibles en la base de datos.');
        }
        $booking = \App\Models\Booking::factory()->create(['user_id' => $user->id, 'professional_id' => $professional->id]);
        $this->actingAs($user);
        $response = $this->post("/professionals/{$professional->id}/rate", [
            'booking_id' => $booking->id,
            'rating' => 5,
            'comment' => 'Excelente servicio',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'professional_id' => $professional->id,
            'rating' => 5,
        ]);
    }

    public function test_user_can_view_services()
    {
        $services = \App\Models\Service::take(3)->get();
        if ($services->isEmpty()) {
            $this->markTestSkipped('No hay servicios disponibles en la base de datos.');
        }
        $response = $this->get('/services');
        $response->assertStatus(200);
        foreach ($services as $service) {
            $response->assertSee($service->name);
        }
    }

    public function test_user_can_filter_services_by_professional()
    {
        $professional = \App\Models\User::whereHas('roles', function($q) { $q->where('slug', 'professional'); })->first();
        if (!$professional) {
            $this->markTestSkipped('No hay profesionales disponibles en la base de datos.');
        }
        $services = \App\Models\Service::where('professional_id', $professional->id)->take(2)->get();
        if ($services->isEmpty()) {
            $this->markTestSkipped('El profesional no tiene servicios asignados.');
        }
        $response = $this->get("/services?professional_id={$professional->id}");
        $response->assertStatus(200);
        foreach ($services as $service) {
            $response->assertSee($service->name);
        }
    }
}
