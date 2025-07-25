<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ProfessionalBookingEdited extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $scheduledAt = Carbon::parse($this->booking->scheduled_at, 'America/Bogota');
        $customerName = $this->booking->customer->first_name . ' ' . $this->booking->customer->last_name;
        
        return (new MailMessage)
            ->subject('Cita Modificada - ' . $customerName)
            ->greeting('Estimado/a ' . $notifiable->name . ',')
            ->line('Te informamos que el cliente ' . $customerName . ' ha modificado su cita.')
            ->line('**Detalles de la cita modificada:**')
            ->line('• **Cliente:** ' . $customerName)
            ->line('• **Servicio:** ' . $this->booking->service->name)
            ->line('• **Nueva fecha y hora:** ' . $scheduledAt->format('d/m/Y H:i'))
            ->line('• **Duración:** ' . $this->booking->service->duration . ' minutos')
            ->line('• **Precio:** $' . number_format($this->booking->service->price, 0, ',', '.'))
            ->lineIf($this->booking->notes, '• **Notas:** ' . $this->booking->notes)
            ->line('Por favor, revisa tu agenda y confirma la disponibilidad para la nueva fecha y hora.')
            ->action('Ver Detalles en el Panel', url('/admin/bookings'))
            ->line('Si tienes alguna pregunta o necesitas contactar al cliente, puedes hacerlo a través del panel de administración.')
            ->salutation('Saludos,<br>Sistema de Gestión Aesthectic');
    }

    public function toArray($notifiable)
    {
        $scheduledAt = Carbon::parse($this->booking->scheduled_at, 'America/Bogota');
        $customerName = $this->booking->customer->first_name . ' ' . $this->booking->customer->last_name;
        
        return [
            'title' => 'Cita editada',
            'message' => 'El cliente ' . $customerName . ' ha modificado su cita para el ' . $scheduledAt->format('d/m/Y H:i'),
            'booking_id' => $this->booking->id,
            'customer_name' => $customerName,
            'service_name' => $this->booking->service->name,
            'scheduled_at' => $scheduledAt->format('d/m/Y H:i'),
            'type' => 'booking_edited',
            'action_url' => '/admin/bookings'
        ];
    }
}
