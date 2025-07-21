<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param Booking $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $scheduledAt = Carbon::parse($this->booking->scheduled_at);
        $endTime = $scheduledAt->copy()->addMinutes((int) $this->booking->service->duration);
        
        // Crear el evento de calendario
        $icsContent = $this->generateIcsContent($scheduledAt, $endTime);
        
        return (new MailMessage)
            ->subject('Confirmación de tu cita en Aesthectic')
            ->greeting('¡Hola ' . $notifiable->first_name . '!')
            ->line('Tu cita ha sido confirmada con éxito.')
            ->line('Detalles de la cita:')
            ->line('Servicio: ' . $this->booking->service->name)
            ->line('Fecha: ' . $scheduledAt->format('d/m/Y'))
            ->line('Hora: ' . $scheduledAt->format('h:i A'))
            ->line('Duración: ' . $this->booking->service->duration . ' minutos')
            ->line('Profesional: ' . $this->booking->professional->name)
            ->action('Ver detalles de la cita', URL::route('booking.confirmation', ['booking' => $this->booking->id]))
            ->line('Gracias por confiar en nosotros.')
            ->attachData($icsContent, 'cita.ics', [
                'mime' => 'text/calendar',
            ]);
    }

    /**
     * Generate ICS content for calendar integration
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return string
     */
    protected function generateIcsContent(Carbon $start, Carbon $end)
    {
        $domain = config('app.url');
        $prodId = '-//' . parse_url($domain, PHP_URL_HOST) . '//Aesthectic//ES';
        $now = Carbon::now()->format('Ymd\THis\Z');
        $uid = uniqid() . '@' . parse_url($domain, PHP_URL_HOST);
        
        // Formatear fechas para ICS
        $dtStart = $start->format('Ymd\THis');
        $dtEnd = $end->format('Ymd\THis');
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:{$prodId}\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:REQUEST\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$now}\r\n";
        $ics .= "DTSTART:{$dtStart}\r\n";
        $ics .= "DTEND:{$dtEnd}\r\n";
        $ics .= "SUMMARY:Cita en Aesthectic - {$this->booking->service->name}\r\n";
        $ics .= "DESCRIPTION:Tu cita para {$this->booking->service->name} con {$this->booking->professional->name}\r\n";
        $ics .= "LOCATION:Aesthectic\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'service' => $this->booking->service->name,
            'scheduled_at' => $this->booking->scheduled_at,
        ];
    }
}