<?php
declare(strict_types=1);

namespace App\Orchid\Screens\Examples;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
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
        $roles = $user ? $user->roles()->pluck('slug')->toArray() : [];
        $isAdmin = in_array('admin', $roles);
        $roleName = $user ? ($user->roles()->first()?->name ?? 'Sin rol') : 'Sin rol';

        // Depuración: Registrar información del usuario y sus roles
        Log::info('Usuario autenticado', [
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'roles' => $roles,
            'is_admin' => $isAdmin,
        ]);

        $selectedDate = $request->input('selected_date', Carbon::today('America/Bogota')->format('Y-m-d'));

        // Para profesionales, siempre filtramos por su ID
        $professionalId = $isAdmin ? null : ($user ? $user->id : null);

        // Base de consultas para citas
        $query = Booking::with(['service', 'professional', 'customer']);
        if ($professionalId) {
            $query->where('professional_id', $professionalId);
        }
        if (!$isAdmin) {
            $query->whereNotIn('status', ['cancelled', 'completed']);
        }

        // Métricas
        $totalBookings = $query->count();
        $bookingsToday = $query->whereDate('scheduled_at', $selectedDate)->count();

        // Ingresos mensuales
        $monthlyRevenueQuery = Booking::where('payment_status', 'paid')
            ->whereMonth('payment_completed_at', Carbon::now('America/Bogota')->month);
        if ($professionalId) {
            $monthlyRevenueQuery->where('professional_id', $professionalId);
        }
        $monthlyRevenue = $monthlyRevenueQuery->sum('total_amount');

        // Ranking de servicios
        $serviceRankingQuery = Booking::select('service_id');
        if ($professionalId) {
            $serviceRankingQuery->where('professional_id', $professionalId);
        }
        if (!$isAdmin) {
            $serviceRankingQuery->whereNotIn('status', ['cancelled', 'completed']);
        }
        $serviceRanking = $serviceRankingQuery
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('service')
            ->get()
            ->map(function ($booking) use ($professionalId, $isAdmin) {
                $countQuery = Booking::where('service_id', $booking->service_id);
                if ($professionalId) {
                    $countQuery->where('professional_id', $professionalId);
                }
                if (!$isAdmin) {
                    $countQuery->whereNotIn('status', ['cancelled', 'completed']);
                }
                return [
                    'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'count' => $countQuery->count(),
                ];
            })->toArray();

        // Lista de profesionales para el calendario
        $professionals = $isAdmin ? User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))
            ->pluck('name', 'id')
            ->toArray() : [];

        // Citas diarias
        $dailyBookingsQuery = Booking::whereDate('scheduled_at', $selectedDate);
        if ($professionalId) {
            $dailyBookingsQuery->where('professional_id', $professionalId);
        }
        if (!$isAdmin) {
            $dailyBookingsQuery->whereNotIn('status', ['cancelled', 'completed']);
        }
        $dailyBookings = $dailyBookingsQuery
            ->with(['service', 'professional', 'customer'])
            ->get()
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
            'role_name' => $roleName,
            'pending_bookings_count' => Booking::where('status', 'pending')
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->count(),
            'professional_id' => $professionalId,
            'status' => $request->input('status', 'all'),
            'selected_professional_name' => $professionalId 
                ? ($professionals[$professionalId] ?? 'Profesional seleccionado')
                : ($isAdmin ? 'Todos los Profesionales' : 'Mi Dashboard'),
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

        return $layouts;
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && in_array('admin', $user->roles()->pluck('slug')->toArray());
            
            $professionalId = $request->input('professional_id');
            $status = $request->input('status', 'all');

            // Para profesionales, siempre filtramos por su ID
            if (!$isAdmin) {
                $professionalId = $user ? $user->id : null;
            }

            $bookingsQuery = Booking::with(['service', 'professional', 'customer']);
            
            // Aplicar filtro por profesional si existe
            if ($professionalId && $professionalId !== '') {
                $bookingsQuery->where('professional_id', $professionalId);
            }

            // Aplicar filtro por estado si no es 'all'
            if ($status !== 'all') {
                $bookingsQuery->where('status', $status);
            } elseif (!$isAdmin) {
                $bookingsQuery->whereNotIn('status', ['cancelled', 'completed']);
            }

            $bookings = $bookingsQuery->get();

            $events = $bookings->map(function ($booking) use ($isAdmin) {
                $startDate = $booking->scheduled_at instanceof \Carbon\Carbon
                    ? Carbon::parse($booking->scheduled_at, 'America/Bogota')->toIso8601String()
                    : Carbon::now('America/Bogota')->toIso8601String();

                $title = is_object($booking->service) ? $booking->service->name : 'Cita';
                if ($isAdmin && is_object($booking->professional)) {
                    $title;
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

    public function updateBookingStatus(Booking $booking, Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            
            if (!in_array($status, ['cancelled', 'completed'])) {
                return response()->json(['error' => 'Estado no válido.'], 400);
            }

            if ($status === 'cancelled') {
                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                if ($scheduledAt instanceof \Carbon\Carbon && Carbon::now('America/Bogota')->gte($scheduledAt->subHour())) {
                    return response()->json(['error' => 'No se puede cancelar menos de 1 hora antes.'], 403);
                }
            }

            $booking->update([
                'status' => $status,
                $status === 'cancelled' ? 'cancelled_at' : 'completed_at' => Carbon::now('America/Bogota'),
            ]);

            return response()->json(['success' => true, 'message' => 'Cita actualizada exitosamente.']);
        } catch (\Exception $e) {
            Log::error('Error in updateBookingStatus: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al actualizar la cita: ' . $e->getMessage()], 500);
        }
    }

    public function applyFilter(Request $request)
    {
        $params = [];
        
        if ($request->has('selected_date')) {
            $params['selected_date'] = $request->input('selected_date');
        }
        if ($request->has('professional_id')) {
            $params['professional_id'] = $request->input('professional_id');
        }
        if ($request->has('status')) {
            $params['status'] = $request->input('status');
        }
        
        Log::info('Apply filter called', $params);
        
        return redirect()->route('platform.dashboard', $params);
    }

    public function refresh(Request $request)
    {
        Log::info('Refresh called', $request->only(['professional_id', 'status', 'selected_date']));
        
        return redirect()->route('platform.dashboard', $request->only(['professional_id', 'status', 'selected_date']));
    }
}