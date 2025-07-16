<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Customer;
use App\Notifications\BookingConfirmation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestBookingNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:test-notification {booking_id? : ID de la reserva para probar} {email? : Correo electrónico para enviar la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío de notificaciones de reserva con integración de calendario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $email = $this->argument('email');

        if (!$bookingId) {
            $bookingId = $this->ask('Ingresa el ID de la reserva para probar la notificación');
        }

        try {
            $booking = Booking::with(['service', 'professional', 'customer'])->findOrFail($bookingId);
            
            $customer = $booking->customer;
            
            if ($email) {
                // Si se proporciona un correo electrónico de prueba, creamos un cliente temporal
                $tempCustomer = new Customer();
                $tempCustomer->email = $email;
                $tempCustomer->first_name = 'Prueba';
                $tempCustomer->last_name = 'Notificación';
                
                $this->info("Enviando notificación de prueba a: {$email}");
                $tempCustomer->notify(new BookingConfirmation($booking));
            } else {
                $this->info("Enviando notificación al cliente: {$customer->email}");
                $customer->notify(new BookingConfirmation($booking));
            }
            
            $this->info('Notificación enviada correctamente.');
            $this->info('Detalles de la reserva:');
            $this->table(
                ['ID', 'Cliente', 'Servicio', 'Profesional', 'Fecha y Hora'],
                [[
                    $booking->id,
                    $booking->customer->first_name . ' ' . $booking->customer->last_name,
                    $booking->service->name,
                    $booking->professional->name,
                    $booking->scheduled_at
                ]]
            );
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error al enviar la notificación: {$e->getMessage()}");
            Log::error("Error en comando TestBookingNotification: {$e->getMessage()}", [
                'exception' => $e,
                'booking_id' => $bookingId
            ]);
            
            return Command::FAILURE;
        }
    }
}