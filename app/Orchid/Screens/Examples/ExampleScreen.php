<?php

namespace App\Orchid\Screens\Examples;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\Layouts\Chart;
use Orchid\Support\Facades\Toast;

class ExampleScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $bookingsChart = Booking::select(
            DB::raw('DATE_FORMAT(scheduled_at, "%Y-%m") as month'),
            DB::raw('count(id) as count')
        )
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        return [
            'charts'  => [
                [
                    'name'   => 'Citas',
                    'values' => $bookingsChart->pluck('count')->toArray(),
                    'labels' => $bookingsChart->pluck('month')->toArray(),
                ],
            ],
            'metrics' => [
                'users'         => ['value' => User::count()],
                'services'      => ['value' => Service::count()],
                'bookings'      => ['value' => Booking::count()],
                'bookings_today' => ['value' => Booking::whereDate('scheduled_at', today())->count()],
                'monthly_revenue' => ['value' => number_format(Booking::where('payment_status', 'paid')->whereMonth('payment_completed_at', now()->month)->sum('total_amount'), 2)],
            ],
            'users' => User::paginate(),
            'customers' => Customer::latest()->take(5)->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Dashboard Principal';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Resumen de datos importantes de la aplicación.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    public function getCalendarEvents(Request $request)
    {
        $bookings = Booking::with('service')->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id'    => $booking->id,
                'title' => $booking->service->name ?? 'Cita',
                'start' => $booking->scheduled_at->toIso8601String(),
                'url'   => route('platform.bookings.edit', $booking),
            ];
        });

        return response()->json($events);
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Total Usuarios'  => 'metrics.users',
                'Total Servicios' => 'metrics.services',
                'Total Citas'     => 'metrics.bookings',
                'Citas para Hoy'    => 'metrics.bookings_today',
                'Ingresos del Mes' => 'metrics.monthly_revenue',
            ]),

            Layout::split([
                Layout::view('vendor.orchid.layouts.calendar'),
                Layout::table('customers', [
                    TD::make('name', 'Últimos Clientes Registrados'),
                    TD::make('email', 'Correo'),
                    TD::make('created_at', 'Fecha Registro')->render(fn ($model) => $model->created_at->format('d/m/Y')),
                ])->title('Clientes Recientes'),
            ])->ratio('70/30'),

            Layout::chart('bookingsChart', 'Citas por Mes')
                ->type('bar')
                ->target('charts'),

            // Ocultamos la tabla de usuarios para dar más espacio, se puede reactivar si es necesario
             Layout::table('users', [
                 TD::make('id', 'ID'),
                 TD::make('name', 'Nombre'),
                 TD::make('email', 'Correo Electrónico'),
             ]),
        ];
    }

}
