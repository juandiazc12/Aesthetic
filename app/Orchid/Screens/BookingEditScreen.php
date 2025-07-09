<?php

namespace App\Orchid\Screens;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BookingEditScreen extends Screen
{
    /**
     * @var Booking
     */
    public $booking;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Booking $booking): iterable
    {
        return [
            'booking' => $booking->load(['service', 'customer', 'professional']),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->booking->exists ? 'Detalles de la Cita' : 'Crear Nueva Cita';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Guardar Cambios')
                ->icon('bs.check-circle')
                ->method('save')
                ->canSee($this->booking->exists),

            Button::make('Eliminar')
                ->icon('bs.trash3')
                ->method('remove')
                ->canSee($this->booking->exists),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Relation::make('booking.customer_id')
                    ->title('Cliente')
                    ->fromModel(Customer::class, 'name')
                    ->disabled(),

                Relation::make('booking.service_id')
                    ->title('Servicio')
                    ->fromModel(Service::class, 'name')
                    ->disabled(),

                Relation::make('booking.professional_id')
                    ->title('Profesional')
                    ->fromModel(User::class, 'name') // Asumiendo que los profesionales son usuarios
                    ->disabled(),

                DateTimer::make('booking.scheduled_at')
                    ->title('Fecha y Hora')
                    ->enableTime()
                    ->format('Y-m-d H:i')
                    ->disabled(),

                TextArea::make('booking.notes')
                    ->title('Notas')
                    ->rows(5)
                    ->disabled(),
            ])
        ];
    }

    public function save(Booking $booking, Request $request)
    {
        // Lógica para guardar aquí (la implementaremos si es necesario)
        Toast::info('Funcionalidad de guardado no implementada.');
    }

    public function remove(Booking $booking)
    {
        // Lógica para eliminar aquí (la implementaremos si es necesario)
        Toast::info('Funcionalidad de eliminación no implementada.');
    }
}
