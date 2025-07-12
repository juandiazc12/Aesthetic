<?php
declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Booking; // Importar el modelo correcto
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BookingEditScreen extends Screen
{
    public $booking;

    public function query(Booking $booking): iterable
    {
        $booking->load(['service', 'professional', 'customer']);
        return [
            'booking' => $booking,
        ];
    }

    public function name(): ?string
    {
        return 'Editar Cita';
    }

    public function permission(): ?iterable
    {
        return [
            'platform.dashboard',
        ];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Guardar')
                ->icon('bs.check-circle')
                ->method('save'),
            Button::make('Cancelar Cita')
                ->icon('bs.trash3')
                ->method('cancel')
                ->canSee($this->booking->status !== 'cancelled'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::rows([
                Select::make('booking.service_id')
                    ->title('Servicio')
                    ->options(Service::pluck('name', 'id')->toArray())
                    ->required(),
                Select::make('booking.professional_id')
                    ->title('Profesional')
                    ->options(User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))->pluck('name', 'id')->toArray())
                    ->required(),
                DateTimer::make('booking.scheduled_at')
                    ->title('Fecha y Hora')
                    ->format('Y-m-d H:i')
                    ->required(),
                Select::make('booking.status')
                    ->title('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->required(),
            ]),
        ];
    }

    public function save(Booking $booking, Request $request)
    {
        $request->validate([
            'booking.service_id' => 'required|exists:services,id',
            'booking.professional_id' => 'required|exists:users,id',
            'booking.scheduled_at' => 'required|date|after:now',
            'booking.status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $existingBooking = Booking::where('professional_id', $request->input('booking.professional_id'))
            ->where('scheduled_at', $request->input('booking.scheduled_at'))
            ->whereNotIn('status', ['cancelled'])
            ->where('id', '!=', $booking->id)
            ->first();

        if ($existingBooking) {
            Toast::error('El horario ya está ocupado.');
            return redirect()->back();
        }

        $booking->update($request->input('booking'));
        Toast::info('Cita actualizada.');
        return redirect()->route('platform.example');
    }

    public function cancel(Booking $booking)
    {
        if (Carbon::now()->gte($booking->scheduled_at->subHour())) {
            Toast::error('No se puede cancelar menos de 1 hora antes.');
            return redirect()->back();
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        Toast::info('Cita cancelada.');
        return redirect()->route('platform.example');
    }
}