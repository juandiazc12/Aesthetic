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

    public function __construct(Booking $booking, $reason = null)
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
        return (new MailMessage)
            ->subject('Tu cita ha sido cancelada')
            ->greeting('Hola ' . $notifiable->first_name . ',')
            ->line('Lamentamos informarte que tu cita ha sido cancelada por el equipo de Aesthectic.')
            ->line($this->reason ? 'Motivo: ' . $this->reason : 'Si necesitas más información, contáctanos.')
            ->line('Te pedimos disculpas por los inconvenientes.')
            ->salutation('Equipo Aesthectic');
    }
}
