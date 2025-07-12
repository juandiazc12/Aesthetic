<?php
declare(strict_types=1);

namespace App\Orchid\Screens\Examples;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ExampleScreen extends Screen
{
    public function query(Request $request): iterable
    {
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin']);
        $professionalId = $isAdmin ? $request->input('professional_id') : ($user ? $user->id : null);

        $query = Booking::where('status', '!=', 'cancelled');
        if ($professionalId) {
            $query->where('professional_id', $professionalId);
        }

        $totalBookings = $query->count();
        $bookingsToday = $query->whereDate('scheduled_at', today())->count();
        $monthlyRevenue = $query->where('payment_status', 'paid')
            ->whereMonth('payment_completed_at', now()->month)
            ->sum('total_amount');

        $serviceRanking = Booking::select('service_id')
            ->where('status', '!=', 'cancelled')
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('service')
            ->get()
            ->map(function ($booking) {
                return [
                    'service_name' => $booking->service->name ?? 'Sin servicio',
                    'count' => Booking::where('service_id', $booking->service_id)
                        ->where('status', '!=', 'cancelled')
                        ->count(),
                ];
            });

        $professionals = $isAdmin ? User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))
            ->pluck('name', 'id')
            ->toArray() : [];

        $selectedDate = $request->input('selected_date', today()->format('Y-m-d'));
        $dailyBookings = Booking::where('status', '!=', 'cancelled')
            ->whereDate('scheduled_at', $selectedDate)
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->with(['service', 'professional', 'customer'])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => $booking->service->name ?? 'Sin servicio',
                    'professional' => $booking->professional->name ?? 'Sin profesional',
                    'customer' => $booking->customer->name ?? 'Sin cliente',
                    'scheduled_at' => $booking->scheduled_at->format('H:i'),
                    'status' => $booking->statusSpanish,
                    'total_amount' => number_format($booking->total_amount, 2),
                ];
            });

        return [
            'metrics' => [
                'total_bookings' => ['value' => $totalBookings],
                'bookings_today' => ['value' => $bookingsToday],
                'monthly_revenue' => ['value' => number_format($monthlyRevenue, 2)],
            ],
            'service_ranking' => $serviceRanking,
            'professionals' => $professionals,
            'daily_bookings' => $dailyBookings,
            'selected_date' => $selectedDate,
            'is_admin' => $isAdmin,
        ];
    }

    public function name(): ?string
    {
        return 'Dashboard';
    }

    public function description(): ?string
    {
        return 'Resumen de citas, ingresos y servicios';
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Actualizar')
                ->icon('bs.arrow-repeat')
                ->method('refresh'),
        ];
    }

    public function layout(): iterable
    {
        $layouts = [
            Layout::metrics([
                'Total de Citas' => 'metrics.total_bookings',
                'Citas de Hoy' => 'metrics.bookings_today',
                'Ingresos Mensuales' => 'metrics.monthly_revenue',
            ]),
            Layout::view('calendar'),
            Layout::table('service_ranking', [
                TD::make('service_name', 'Servicio'),
                TD::make('count', 'Número de Citas'),
            ])->title('Ranking de Servicios'),
            Layout::table('daily_bookings', [
                TD::make('service', 'Servicio'),
                TD::make('professional', 'Profesional'),
                TD::make('customer', 'Cliente'),
                TD::make('scheduled_at', 'Hora'),
                TD::make('status', 'Estado'),
                TD::make('total_amount', 'Monto'),
            ])->title('Citas del Día Seleccionado'),
        ];

        $user = auth()->user();
        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin'])) {
            array_splice($layouts, 1, 0, [
                Layout::rows([
                    Select::make('professional_id')
                        ->options(['' => 'Todos los Profesionales'] + $this->query()['professionals'])
                        ->title('Filtrar por Profesional')
                        ->empty('Todos los Profesionales')
                        ->value(request()->input('professional_id')),
                    Select::make('selected_date')
                        ->title('Seleccionar Fecha')
                        ->type('date')
                        ->value(request()->input('selected_date', today()->format('Y-m-d'))),
                ])->title('Filtros'),
            ]);
        }

        return $layouts;
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $professionalId = ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin'])) ? $request->input('professional_id') : ($user ? $user->id : null);

            $bookings = Booking::with('service')
                ->where('status', '!=', 'cancelled')
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->get();

            $events = $bookings->map(function ($booking) {
                // Asegurar que scheduled_at sea una fecha válida
                $startDate = $booking->scheduled_at && $booking->scheduled_at->isValid()
                    ? $booking->scheduled_at->toIso8601String()
                    : now()->toIso8601String();

                return [
                    'id' => $booking->id,
                    'title' => $booking->service->name ?? 'Cita',
                    'start' => $startDate,
                    'url' => route('platform.bookings.edit', $booking),
                    'extendedProps' => [
                        'canCancel' => Carbon::now()->lt($booking->scheduled_at && $booking->scheduled_at->isValid() ? $booking->scheduled_at->subHour() : now()),
                    ],
                ];
            })->toArray();

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in getCalendarEvents: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cargar los eventos: ' . $e->getMessage()], 500);
        }
    }

    public function cancelBooking(Booking $booking)
    {
        if (Carbon::now()->gte($booking->scheduled_at && $booking->scheduled_at->isValid() ? $booking->scheduled_at->subHour() : now())) {
            Toast::error('No se puede cancelar menos de 1 hora antes.');
            return redirect()->back();
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        Toast::info('Cita cancelada exitosamente.');
        return redirect()->route('platform.example');
    }

    public function refresh(Request $request)
    {
        return redirect()->route('platform.example', $request->only(['professional_id', 'selected_date']));
    }
}