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
        
        // Para admin: si no se especifica professional_id, mostramos todas las citas
        // Para profesional: siempre mostramos solo sus citas
        if ($isAdmin) {
            $professionalId = null; // Admin nunca filtra por profesional
        } else {
            $professionalId = $user ? $user->id : null;
        }

        // Calculate pending bookings for the notification
        $pendingBookingsQuery = Booking::where('status', 'pending');
        if (!$isAdmin && $professionalId) {
            $pendingBookingsQuery->where('professional_id', $professionalId);
        } elseif ($isAdmin && $professionalId) {
            $pendingBookingsQuery->where('professional_id', $professionalId);
        }
        $pendingBookingsCount = $pendingBookingsQuery->count();

        // Total de citas (todas las que no están canceladas ni completadas)
        $totalBookings = Booking::whereNotIn('status', ['cancelled', 'completed'])->count();

        // Citas de hoy
        $bookingsToday = Booking::whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('scheduled_at', Carbon::today('America/Bogota'))
            ->count();

        // Ingresos mensuales
        $monthlyRevenue = Booking::where('payment_status', 'paid')
            ->whereMonth('payment_completed_at', Carbon::now('America/Bogota')->month)
            ->sum('total_amount');

        // Ranking de servicios
        $serviceRanking = Booking::select('service_id')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('service')
            ->get()
            ->map(function ($booking) {
                $count = Booking::where('service_id', $booking->service_id)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->count();
                return [
                    'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'count' => $count,
                ];
            })->toArray();

        // Get professionals list for admin filter
        $professionals = $isAdmin ? User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))
            ->pluck('name', 'id')
            ->toArray() : [];

        $selectedDate = $request->input('selected_date', Carbon::today('America/Bogota')->format('Y-m-d'));

        // Citas del día seleccionado
        // Filtrado por rango de fecha en zona horaria Bogota (convertido a UTC)
        $start = Carbon::parse($selectedDate . ' 00:00:00', 'America/Bogota')->setTimezone('UTC');
        $end = Carbon::parse($selectedDate . ' 23:59:59', 'America/Bogota')->setTimezone('UTC');
        $dailyBookingsQuery = Booking::whereNotIn('status', ['cancelled', 'completed'])
            ->whereBetween('scheduled_at', [$start, $end]);
        // Solo filtra por profesional si NO es admin
        if (!$isAdmin && $professionalId) {
            $dailyBookingsQuery->where('professional_id', $professionalId);
        }
        // Logging temporal para depuración
        \Log::debug('DASHBOARD ADMIN - Rango de búsqueda', [
            'selectedDate' => $selectedDate,
            'start_utc' => $start->toDateTimeString(),
            'end_utc' => $end->toDateTimeString(),
        ]);
        $dailyBookingsRaw = $dailyBookingsQuery
            ->with(['service', 'professional', 'customer'])
            ->get();
        \Log::debug('DASHBOARD ADMIN - Bookings encontrados', [
            'count' => $dailyBookingsRaw->count(),
            'ids' => $dailyBookingsRaw->pluck('id')->toArray(),
            'fechas' => $dailyBookingsRaw->pluck('scheduled_at')->toArray(),
        ]);
        $dailyBookings = $dailyBookingsRaw
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                    'customer' => is_object($booking->customer) ? $booking->customer->name : 'Sin cliente',
                    'scheduled_at' => $booking->scheduled_at instanceof \Carbon\Carbon ? 
                        Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i') : 'Sin horario',
                    'duration' => is_object($booking->service) ? $booking->service->duration : null,
                    'status' => $booking->statusSpanish ?? 'Desconocido',
                    'total_amount' => $booking->total_amount !== null ? 
                        number_format((float)$booking->total_amount, 0) : '0',
                ];
            })->toArray();

        return [
            'metrics' => [
                'total_bookings' => ['value' => $totalBookings],
                'bookings_today' => ['value' => $bookingsToday],
                'monthly_revenue' => ['value' => $monthlyRevenue !== null ? 
                    number_format((float)$monthlyRevenue, 0) : '0'],
            ],
            'service_ranking' => $serviceRanking,
            'professionals' => $professionals,
            'daily_bookings' => $dailyBookings,
            'selected_date' => $selectedDate,
            'is_admin' => $isAdmin,
            'pending_bookings_count' => $pendingBookingsCount,
            'professional_id' => $professionalId,
            'selected_professional_name' => $professionalId ? 
                ($professionals[$professionalId] ?? 'Profesional seleccionado') : 
                ($isAdmin ? 'Todos los Profesionales' : 'Mi Dashboard'),
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
        ];



        $layouts[] = Layout::view('calendar');

        $layouts[] = Layout::table('service_ranking', [
            TD::make('service_name', 'Servicio')->render(fn ($item) => $item['service_name']),
            TD::make('count', 'Número de Citas')->render(fn ($item) => $item['count']),
        ])->title('Ranking de Servicios');

        $layouts[] = Layout::table('daily_bookings', [
            TD::make('service', 'Servicio')->render(fn ($item) => $item['service']),
            TD::make('professional', 'Profesional')->render(fn ($item) => $item['professional']),
            TD::make('customer', 'Cliente')->render(fn ($item) => $item['customer']),
            TD::make('scheduled_at', 'Hora')->render(fn ($item) => $item['scheduled_at']),
            TD::make('duration', 'Duración (min)')->render(fn ($item) => $item['duration']),
            TD::make('status', 'Estado')->render(fn ($item) => $item['status']),
            TD::make('total_amount', 'Monto')->render(fn ($item) => $item['total_amount']),
        ])->title('Citas del Día Seleccionado');

        return $layouts;
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin']);
            
            // Filtros recibidos por request
            $filterProfessionalId = $request->input('calendar_professional_id');
            $filterStatus = $request->input('calendar_status'); // 'pending', 'active', 'completed', 'cancelled', 'all'

            // Para admin: si no se especifica professional_id, mostramos todas las citas
            // Para profesional: siempre mostramos solo sus citas
            if ($isAdmin) {
                $professionalId = null; // Admin nunca filtra por profesional
            } else {
                $professionalId = $user ? $user->id : null;
            }

            $bookingsQuery = Booking::with(['service', 'professional', 'customer']);

            // Filtro por profesional (admin puede ver todos o uno)
            if ($isAdmin && $filterProfessionalId && $filterProfessionalId !== 'all') {
                $bookingsQuery->where('professional_id', $filterProfessionalId);
            } elseif (!$isAdmin && $professionalId) {
                $bookingsQuery->where('professional_id', $professionalId);
            }

            // Filtro por estado
            if ($filterStatus && $filterStatus !== 'all') {
                $bookingsQuery->where('status', $filterStatus);
            } else {
                // Por default, solo mostrar activas o pendientes
                $bookingsQuery->whereNotIn('status', ['cancelled', 'completed']);
            }

            $bookings = $bookingsQuery->get();

            $events = $bookings->map(function ($booking) use ($isAdmin) {
                $startDate = $booking->scheduled_at instanceof \Carbon\Carbon
                    ? Carbon::parse($booking->scheduled_at, 'America/Bogota')->toIso8601String()
                    : Carbon::now('America/Bogota')->toIso8601String();

                // Para admin, incluir el nombre del profesional en el título
                $title = is_object($booking->service) ? $booking->service->name : 'Cita';
                if ($isAdmin && is_object($booking->professional)) {
                    $title .= ' - ' . $booking->professional->name;
                }

                return [
                    'id' => $booking->id,
                    'title' => $title,
                    'start' => $startDate,
                    'url' => route('platform.bookings.edit', $booking),
                    'extendedProps' => [
                        'canCancel' => $booking->scheduled_at instanceof \Carbon\Carbon
                            ? Carbon::now('America/Bogota')->lt(Carbon::parse($booking->scheduled_at, 'America/Bogota')->subHour())
                            : false,
                        'status' => $booking->statusSpanish ?? $booking->status,
                        'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                        'customer' => is_object($booking->customer) ? $booking->customer->name : 'Sin cliente',
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

    public function applyFilter(Request $request)
    {
        $params = [];
        
        // Mantener la fecha seleccionada si existe
        if ($request->has('selected_date')) {
            $params['selected_date'] = $request->input('selected_date');
        }
        

        
        Log::info('Apply filter called', $params);
        
        return redirect()->route('platform.dashboard', $params);
    }

    public function refresh(Request $request)
    {
        Log::info('Refresh called', $request->only(['selected_date']));
        
        return redirect()->route('platform.dashboard', $request->only(['selected_date']));
    }
}