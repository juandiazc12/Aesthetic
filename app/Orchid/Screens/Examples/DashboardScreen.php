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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardScreen extends Screen
{
    public function query(Request $request): array
    {
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin']);
        $professionalId = $isAdmin ? $request->input('professional_id') : ($user ? $user->id : null);

        // Calculate pending bookings for the notification
        $pendingBookingsQuery = Booking::where('status', 'pending');
        if (!$isAdmin && $professionalId) {
            $pendingBookingsQuery->where('professional_id', $professionalId);
        }
        $pendingBookingsCount = $pendingBookingsQuery->count();

        // Metrics query: For admins, show all unless professional_id is specified
        $query = Booking::whereNotIn('status', ['cancelled', 'completed']);
        if ($professionalId && (!$isAdmin || $request->input('professional_id') !== '')) {
            $query->where('professional_id', $professionalId);
        }

        $totalBookings = $query->count();
        $bookingsToday = $query->whereDate('scheduled_at', Carbon::today('America/Bogota'))->count();
        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->whereMonth('payment_completed_at', Carbon::now('America/Bogota')->month)
            ->when($professionalId && (!$isAdmin || $request->input('professional_id') !== ''), fn($q) => $q->where('professional_id', $professionalId))
            ->sum('total_amount');

        $serviceRanking = Booking::select('service_id')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->when($professionalId && (!$isAdmin || $request->input('professional_id') !== ''), fn($q) => $q->where('professional_id', $professionalId))
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('service')
            ->get()
            ->map(function ($booking) use ($isAdmin, $professionalId, $request) {
                $countQuery = Booking::where('service_id', $booking->service_id)
                    ->whereNotIn('status', ['cancelled', 'completed']);
                if ($professionalId && (!$isAdmin || $request->input('professional_id') !== '')) {
                    $countQuery->where('professional_id', $professionalId);
                }
                $result = [
                    'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'count' => $countQuery->count(),
                ];
                Log::debug('Service ranking item', $result);
                return $result;
            })->toArray();

        $professionals = $isAdmin ? User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))
            ->pluck('name', 'id')
            ->toArray() : [];

        $selectedDate = $request->input('selected_date', Carbon::today('America/Bogota')->format('Y-m-d'));
        $dailyBookings = Booking::whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('scheduled_at', $selectedDate)
            ->when($professionalId && (!$isAdmin || $request->input('professional_id') !== ''), fn($q) => $q->where('professional_id', $professionalId))
            ->with(['service', 'professional', 'customer'])
            ->get()
            ->map(function ($booking) {
                $result = [
                    'id' => $booking->id,
                    'service' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                    'customer' => is_object($booking->customer) ? $booking->customer->name : 'Sin cliente',
                    'scheduled_at' => $booking->scheduled_at instanceof \Carbon\Carbon ? Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i') : 'Sin horario',
                    'duration' => is_object($booking->service) ? $booking->service->duration : null,
                    'status' => $booking->statusSpanish ?? 'Desconocido',
                    'total_amount' => $booking->total_amount !== null ? number_format((float)$booking->total_amount, 0) : '0',
                ];
                Log::debug('Daily booking item', $result);
                return $result;
            })->toArray();

        return [
            'metrics' => [
                'total_bookings' => ['value' => $totalBookings],
                'bookings_today' => ['value' => $bookingsToday],
                'monthly_revenue' => ['value' => $monthlyRevenue !== null ? number_format((float)$monthlyRevenue, 0) : '0'],
            ],
            'service_ranking' => $serviceRanking,
            'professionals' => $professionals,
            'daily_bookings' => $dailyBookings,
            'selected_date' => $selectedDate,
            'is_admin' => $isAdmin,
            'pending_bookings_count' => $pendingBookingsCount,
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
        $queryData = $this->query(request());

        $layouts = [
            Layout::metrics([
                'Total de Citas' => 'metrics.total_bookings',
                'Citas de Hoy' => 'metrics.bookings_today',
                'Ingresos Mensuales' => 'metrics.monthly_revenue',
            ]),
            Layout::view('calendar'),
            Layout::table('service_ranking', [
                TD::make('service_name', 'Servicio')->render(fn ($item) => $item['service_name']),
                TD::make('count', 'Número de Citas')->render(fn ($item) => $item['count']),
            ])->title('Ranking de Servicios'),
            Layout::table('daily_bookings', [
                TD::make('service', 'Servicio')->render(fn ($item) => $item['service']),
                TD::make('professional', 'Profesional')->render(fn ($item) => $item['professional']),
                TD::make('customer', 'Cliente')->render(fn ($item) => $item['customer']),
                TD::make('scheduled_at', 'Hora')->render(fn ($item) => $item['scheduled_at']),
                TD::make('duration', 'Duración (min)')->render(fn ($item) => $item['duration']),
                TD::make('status', 'Estado')->render(fn ($item) => $item['status']),
                TD::make('total_amount', 'Monto')->render(fn ($item) => $item['total_amount']),
            ])->title('Citas del Día Seleccionado'),
        ];

        $user = auth()->user();
        if ($user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin'])) {
            array_splice($layouts, 1, 0, [
                Layout::rows([
                    Select::make('professional_id')
                        ->options(['' => 'Todos los Profesionales'] + $queryData['professionals'])
                        ->title('Filtrar por Profesional')
                        ->empty('Todos los Profesionales')
                        ->value($queryData['professional_id'] ?? ''),
                ])->title('Filtros'),
            ]);
        }

        return $layouts;
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin']);
            $professionalId = $isAdmin ? $request->input('professional_id') : ($user ? $user->id : null);

            $bookings = Booking::with('service')
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->when($professionalId && (!$isAdmin || $request->input('professional_id') !== ''), fn($q) => $q->where('professional_id', $professionalId))
                ->get();

            $events = $bookings->map(function ($booking) {
                $startDate = $booking->scheduled_at instanceof \Carbon\Carbon
                    ? Carbon::parse($booking->scheduled_at, 'America/Bogota')->toIso8601String()
                    : Carbon::now('America/Bogota')->toIso8601String();

                return [
                    'id' => $booking->id,
                    'title' => is_object($booking->service) ? $booking->service->name : 'Cita',
                    'start' => $startDate,
                    'url' => route('platform.bookings.edit', $booking),
                    'extendedProps' => [
                        'canCancel' => $booking->scheduled_at instanceof \Carbon\Carbon
                            ? Carbon::now('America/Bogota')->lt(Carbon::parse($booking->scheduled_at, 'America/Bogota')->subHour())
                            : false,
                        'status' => $booking->statusSpanish,
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

    public function cancelBooking(Booking $booking): JsonResponse
    {
        try {
            $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
            if ($scheduledAt instanceof \Carbon\Carbon && Carbon::now('America/Bogota')->gte($scheduledAt->subHour())) {
                return response()->json(['error' => 'No se puede cancelar menos de 1 hora antes.'], 403);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now('America/Bogota'),
            ]);

            return response()->json(['success' => true, 'message' => 'Cita cancelada exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error in cancelBooking: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cancelar la cita: ' . $e->getMessage()], 500);
        }
    }

    public function refresh(Request $request)
    {
        Log::info('Refresh called', $request->only(['professional_id', 'selected_date']));
        return redirect()->route('platform.dashboard', $request->only(['professional_id', 'selected_date']));
    }
}