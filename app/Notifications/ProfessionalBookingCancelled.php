<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ProfessionalBookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $reason;

    public function __construct(Booking $booking, $reason = null)
    {
        $this->booking = $booking;
        $this->reason = $reason ?? 'Cancelada por el cliente';
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
            ->subject('Cita Cancelada - ' . $customerName)
            ->greeting('Estimado/a ' . $notifiable->name . ',')
            ->line('Te informamos que el cliente ' . $customerName . ' ha cancelado su cita.')
            ->line('**Detalles de la cita cancelada:**')
            ->line('• **Cliente:** ' . $customerName)
            ->line('• **Servicio:** ' . $this->booking->service->name)
            ->line('• **Fecha y hora:** ' . $scheduledAt->format('d/m/Y H:i'))
            ->line('• **Duración:** ' . $this->booking->service->duration . ' minutos')
            ->line('• **Precio:** $' . number_format($this->booking->service->price, 0, ',', '.'))
            ->lineIf($this->reason, '• **Motivo:** ' . $this->reason)
            ->lineIf($this->booking->notes, '• **Notas originales:** ' . $this->booking->notes)
            ->line('Este horario ahora está disponible en tu agenda para nuevas reservas.')
            ->action('Ver Agenda', url('/admin/bookings'))
            ->line('Si necesitas más información sobre esta cancelación, puedes revisar los detalles en el panel de administración.')
            ->salutation('Saludos,<br>Sistema de Gestión Aesthectic');
    }

    public function toArray($notifiable)
    {
        $scheduledAt = Carbon::parse($this->booking->scheduled_at, 'America/Bogota');
        $customerName = $this->booking->customer->first_name . ' ' . $this->booking->customer->last_name;
        
        return [
            'title' => 'Cita cancelada',
            'message' => 'El cliente ' . $customerName . ' ha cancelado su cita del ' . $scheduledAt->format('d/m/Y H:i'),
            'booking_id' => $this->booking->id,
            'customer_name' => $customerName,
            'service_name' => $this->booking->service->name,
            'scheduled_at' => $scheduledAt->format('d/m/Y H:i'),
            'reason' => $this->reason,
            'type' => 'booking_cancelled',
            'action_url' => '/admin/bookings'
        ];
    }
}
