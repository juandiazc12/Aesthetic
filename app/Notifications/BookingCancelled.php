<?php
namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $reason;

    public function __construct(Booking $booking, $reason)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $scheduledAt = $this->booking->scheduled_at
            ? \Carbon\Carbon::parse($this->booking->scheduled_at, 'America/Bogota')->format('d/m/Y H:i')
            : 'No especificada';

        return (new MailMessage)
            ->subject('Cancelación de Cita - Aesthectic')
            ->greeting('Estimado/a ' . ($notifiable->first_name ?? $notifiable->name ?? 'Cliente') . ',')
            ->line('Nos dirigimos a usted para informarle que su cita programada para el ' . $scheduledAt . ' ha sido cancelada.')
            ->lineIf($this->reason, 'Motivo de la cancelación: ' . $this->reason)
            ->line('Entendemos que esta situación puede generar inconvenientes y nos disculpamos sinceramente por ello.')
            ->line('Nuestro equipo está disponible para reprogramar su cita en el horario que mejor se adapte a sus necesidades.')
            ->action('Reagendar Cita', url('/bookings/reschedule/' . $this->booking->id))
            ->line('Para cualquier consulta o asistencia adicional, no dude en contactarnos a través de nuestros canales de atención al cliente.')
            ->line('Agradecemos su comprensión y esperamos poder atenderle pronto.')
            ->salutation('Atentamente,<br>Equipo de Atención al Cliente<br>Aesthectic');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'message' => 'Tu cita ha sido cancelada: ' . $this->reason,
        ];
    }
}