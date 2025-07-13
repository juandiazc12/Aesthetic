<?php
namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateCompletedBookings extends Command
{
    protected $signature = 'bookings:update-completed';
    protected $description = 'Actualiza el estado de las citas confirmadas a completadas después de su duración';

    public function handle()
    {
        try {
            $now = Carbon::now('America/Bogota');

            // Obtener citas confirmadas que no estén completadas ni canceladas
            $bookings = Booking::where('status', 'confirmed')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->with('service')
                ->get();

            foreach ($bookings as $booking) {
                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                $duration = $booking->service->duration ?? 30; // Duración por defecto: 30 minutos
                $endTime = $scheduledAt->copy()->addMinutes($duration);

                if ($now->greaterThanOrEqualTo($endTime)) {
                    $booking->update([
                        'status' => 'completed',
                        'completed_at' => $now,
                    ]);

                    Log::info('Cita marcada como completada', [
                        'booking_id' => $booking->id,
                        'scheduled_at' => $booking->scheduled_at,
                        'duration' => $duration,
                        'end_time' => $endTime,
                        'completed_at' => $now,
                    ]);
                }
            }

            $this->info('Citas verificadas y actualizadas exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar citas completadas: ' . $e->getMessage());
            $this->error('Error al actualizar citas: ' . $e->getMessage());
        }
    }
}
