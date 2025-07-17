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
            // Configurar la zona horaria de Colombia
            $timezone = 'America/Bogota';
            $now = Carbon::now($timezone);
            
            // Obtener citas confirmadas que no estén completadas ni canceladas
            $bookings = Booking::where('status', 'confirmed')
                ->with('service')
                ->get();

            $updatedCount = 0;
            
            foreach ($bookings as $booking) {
                // Parsear la fecha desde la DB y convertir a zona horaria de Colombia
                $scheduledAt = Carbon::parse($booking->scheduled_at)->setTimezone($timezone);
                
                // Obtener la duración del servicio
                $duration = $booking->service->duration ?? 30; // Duración por defecto: 30 minutos
                
                // Calcular la hora de finalización (hora de la cita + duración)
                $endTime = $scheduledAt->copy()->addMinutes($duration);
                
                // Verificar si ya ha pasado la hora de finalización
                if ($now->greaterThanOrEqualTo($endTime)) {
                    $booking->update([
                        'status' => 'completed',
                        'completed_at' => $now->utc(), // Guardar en UTC en la DB
                    ]);
                    
                    $updatedCount++;
                    
                    Log::info('Cita marcada como completada automáticamente', [
                        'booking_id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'professional_id' => $booking->professional_id,
                        'service_name' => $booking->service->name,
                        'scheduled_at_utc' => $booking->scheduled_at,
                        'scheduled_at_bogota' => $scheduledAt->format('Y-m-d H:i:s'),
                        'duration_minutes' => $duration,
                        'end_time_bogota' => $endTime->format('Y-m-d H:i:s'),
                        'completed_at_bogota' => $now->format('Y-m-d H:i:s'),
                        'now_utc' => $now->utc()->format('Y-m-d H:i:s'),
                    ]);
                }
            }
            
            if ($updatedCount > 0) {
                $this->info("✅ Se completaron automáticamente {$updatedCount} citas.");
            } else {
                $this->info("ℹ️  No hay citas pendientes de completar en este momento.");
                $this->info("🕐 Hora actual (Bogotá): " . $now->format('Y-m-d H:i:s'));
            }
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar citas completadas: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            $this->error('❌ Error al actualizar citas: ' . $e->getMessage());
        }
    }
}