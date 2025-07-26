<?php
/**
 * Pruebas de reservas para clientes (crear, cancelar, ver historial).
 *
 * NOTA: Los servicios y profesionales se crean y gestionan desde Orchid.
 * Los clientes (customer) se gestionan desde Laravel/factories.
 * Estas pruebas sólo simulan reservas hechas por clientes reales.
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    // Aquí se agregarán las pruebas para:
    // - Crear reserva
    // - Cancelar reserva
    // - Ver historial de reservas
    public function test_user_can_create_booking()
    {
        $user = \App\Models\User::factory()->create();
        $service = \App\Models\Service::first();
        if (!$service) {
            $this->markTestSkipped('No hay servicios disponibles en la base de datos.');
        }
        $this->actingAs($user);

        $response = $this->post('/booking', [
            'service_id' => $service->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '10:00',
        ]);

        $response->assertStatus(302); // Redirige después de crear reserva
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);
    }

    public function test_user_can_cancel_booking()
    {
        $user = \App\Models\User::factory()->create();
        $booking = \App\Models\Booking::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->delete("/bookings/{$booking->id}");
        $response->assertStatus(302); // Redirige después de cancelar
        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);
    }

    public function test_user_can_view_booking_history()
    {
        $user = \App\Models\User::factory()->create();
        $bookings = \App\Models\Booking::factory()->count(3)->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get('/bookings/history');
        $response->assertStatus(200);
        foreach ($bookings as $booking) {
            $response->assertSee((string) $booking->id);
        }
    }
}
