<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class BookingEdited extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $scheduledAt = Carbon::parse($this->booking->scheduled_at);
        $endTime = $scheduledAt->copy()->addMinutes((int) $this->booking->service->duration);
        return (new MailMessage)
    ->subject('Tu cita ha sido modificada')
    ->markdown('emails.booking-edited', [
        'booking' => $this->booking,
        'notifiable' => $notifiable,
    ]);
    }
}
