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

        Log::info('Usuario autenticado', [
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'roles' => $roles,
            'is_admin' => $isAdmin,
        ]);

        $selectedDate = $request->input('selected_date', Carbon::today('America/Bogota')->format('Y-m-d'));
        $professionalId = $request->input('professional_id', '');
        $status = $request->input('status', 'all');
        $month = $request->input('month', Carbon::today('America/Bogota')->month);
        $year = $request->input('year', Carbon::today('America/Bogota')->year);

        if (!$isAdmin) {
            $professionalId = $user ? $user->id : null;
        }

        $baseQuery = Booking::with(['service', 'professional', 'customer'])
            ->whereMonth('scheduled_at', $month)
            ->whereYear('scheduled_at', $year)
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->when($status !== 'all', fn($q) => $q->where('status', $status));

        $totalBookings = (clone $baseQuery)->count();

        $bookingsToday = Booking::whereDate('scheduled_at', Carbon::today('America/Bogota')->format('Y-m-d'))
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->count();

        $monthlyRevenueQuery = Booking::where('payment_status', 'paid')
            ->whereMonth('payment_completed_at', $month)
            ->whereYear('payment_completed_at', $year)
            ->whereIn('status', ['pending', 'completed'])
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->when($status !== 'all', fn($q) => $q->where('status', $status));
        $monthlyRevenue = $monthlyRevenueQuery->sum('total_amount');

        $serviceRanking = (clone $baseQuery)
            ->select('service_id')
            ->groupBy('service_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take(5)
            ->with('service')
            ->get()
            ->map(function ($booking) use ($professionalId, $status, $month, $year) {
                $countQuery = Booking::where('service_id', $booking->service_id)
                    ->whereMonth('scheduled_at', $month)
                    ->whereYear('scheduled_at', $year)
                    ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                    ->when($status !== 'all', fn($q) => $q->where('status', $status));
                return [
                    'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'count' => $countQuery->count(),
                ];
            })->toArray();

        $professionals = $isAdmin ? User::whereHas('roles', fn($q) => $q->where('slug', 'profesional'))
            ->pluck('name', 'id')
            ->toArray() : [];

        $dailyBookings = (clone $baseQuery)
            ->whereDate('scheduled_at', $selectedDate)
            ->get()
            ->map(function ($booking) {
                Log::debug('Booking customer data', [
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'customer_exists' => is_object($booking->customer),
                    'customer_data' => $booking->customer ? $booking->customer->toArray() : null,
                    'customer_name' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                ]);
                return [
                    'id' => $booking->id,
                    'service' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                    'customer' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                    'scheduled_at' => $booking->scheduled_at instanceof \Carbon\Carbon ?
                        Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i') : 'Sin horario',
                    'duration' => is_object($booking->service) ? $booking->service->duration : null,
                    'status' => $booking->statusSpanish ?? 'Desconocido',
                    'total_amount' => $booking->total_amount !== null ?
                        number_format((float) $booking->total_amount, 0) : '0',
                ];
            })->toArray();

        return [
            'metrics' => [
                'total_bookings' => ['value' => $totalBookings],
                'bookings_today' => ['value' => $bookingsToday],
                'monthly_revenue' => [
                    'value' => $monthlyRevenue !== null ?
                        number_format((float) $monthlyRevenue, 0) : '0'
                ],
            ],
            'service_ranking' => $serviceRanking,
            'professionals' => $professionals,
            'daily_bookings' => $dailyBookings,
            'selected_date' => $selectedDate,
            'is_admin' => $isAdmin,
            'role_name' => $roleName,
            'pending_bookings_count' => (clone $baseQuery)->where('status', 'pending')->count(),
            'professional_id' => $professionalId,
            'status' => $status,
            'month' => (int) $month,
            'year' => (int) $year,
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
            Layout::view('dashboard-metrics-container', [
                'metrics' => $queryData['metrics'],
            ]),
            Layout::view('calendar'),
            Layout::view('service-ranking-container', [
                'service_ranking' => $queryData['service_ranking'],
            ]),
            Layout::table('daily_bookings', [
                TD::make('service', 'Servicio')->render(fn($item) => $item['service']),
                TD::make('professional', 'Profesional')->render(fn($item) => $item['professional']),
                TD::make('customer', 'Cliente')->render(fn($item) => $item['customer']),
                TD::make('scheduled_at', 'Hora')->render(fn($item) => $item['scheduled_at']),
                TD::make('duration', 'Duración (min)')->render(fn($item) => $item['duration']),
                TD::make('status', 'Estado')->render(fn($item) => $item['status']),
                TD::make('total_amount', 'Monto')->render(fn($item) => $item['total_amount']),
            ])->title('Citas del Día Seleccionado'),
        ];

        return $layouts;
    }

    public function getCalendarEvents(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && in_array('admin', $user->roles()->pluck('slug')->toArray());

            $professionalId = $request->input('professional_id', '');
            $status = $request->input('status', 'all');
            $start = $request->input('start', Carbon::now('America/Bogota')->startOfMonth()->toDateString());
            $end = $request->input('end', Carbon::now('America/Bogota')->endOfMonth()->toDateString());

            Log::debug('Parámetros recibidos:', [
                'professional_id' => $professionalId,
                'status' => $status,
                'start' => $start,
                'end' => $end
            ]);

            if (!$isAdmin) {
                $professionalId = $user ? $user->id : null;
            }

            $bookingsQuery = Booking::with(['service', 'professional', 'customer'])
                ->whereBetween('scheduled_at', [$start, $end])
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status));

            $bookings = $bookingsQuery->get();
            Log::debug('Reservas encontradas:', ['count' => $bookings->count(), 'bookings' => $bookings->toArray()]);

            $events = $bookings->map(function ($booking) use ($isAdmin) {
                $startDate = $booking->scheduled_at instanceof \Carbon\Carbon
                    ? Carbon::parse($booking->scheduled_at, 'America/Bogota')->toIso8601String()
                    : Carbon::now('America/Bogota')->toIso8601String();

                $title = is_object($booking->service) ? $booking->service->name : 'Cita';
                if ($isAdmin && is_object($booking->professional)) {
                    $title .= ' (' . $booking->professional->name . ')';
                }

                return [
                    'id' => $booking->id,
                    'title' => $title,
                    'start' => $startDate,
                    'url' => route('platform.bookings.edit', $booking),
                    'extendedProps' => [
                        'status' => $booking->statusSpanish ?? $booking->status,
                        'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                        'customer' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                    ],
                ];
            })->toArray();

            Log::debug('Eventos generados para el calendario:', [
                'count' => count($events),
                'events' => $events,
            ]);

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in getCalendarEvents: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cargar los eventos: ' . $e->getMessage()], 500);
        }
    }

    public function getDailyBookings(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && in_array('admin', $user->roles()->pluck('slug')->toArray());

            $selectedDate = $request->input('selected_date', Carbon::today('America/Bogota')->format('Y-m-d'));
            $professionalId = $request->input('professional_id', '');
            $status = $request->input('status', 'all');

            if (!$isAdmin) {
                $professionalId = $user ? $user->id : null;
            }

            $dailyBookingsQuery = Booking::with(['service', 'professional', 'customer'])
                ->whereDate('scheduled_at', $selectedDate)
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status));

            $dailyBookings = $dailyBookingsQuery
                ->get()
                ->map(function ($booking) {
                    Log::debug('Daily bookings customer data', [
                        'booking_id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'customer_exists' => is_object($booking->customer),
                        'customer_data' => $booking->customer ? $booking->customer->toArray() : null,
                        'customer_name' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                    ]);
                    return [
                        'id' => $booking->id,
                        'service' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                        'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                        'customer' => is_object($booking->customer) ? ($booking->customer->first_name ?? $booking->customer->name ?? 'Sin nombre') : 'Sin cliente',
                        'scheduled_at' => $booking->scheduled_at instanceof \Carbon\Carbon ?
                            Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i') : 'Sin horario',
                        'duration' => is_object($booking->service) ? $booking->service->duration : null,
                        'status' => $booking->statusSpanish ?? 'Desconocido',
                        'total_amount' => $booking->total_amount !== null ?
                            number_format((float) $booking->total_amount, 0) : '0',
                    ];
                })->toArray();

            $bookingsToday = Booking::whereDate('scheduled_at', Carbon::today('America/Bogota')->format('Y-m-d'))
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->count();

            return response()->json([
                'daily_bookings' => $dailyBookings,
                'bookings_today' => $bookingsToday,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDailyBookings: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cargar las citas: ' . $e->getMessage()], 500);
        }
    }

    public function getMetricsAndRanking(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $isAdmin = $user && in_array('admin', $user->roles()->pluck('slug')->toArray());

            $professionalId = $request->input('professional_id', '');
            $status = $request->input('status', 'all');
            $month = $request->input('month', Carbon::today('America/Bogota')->month);
            $year = $request->input('year', Carbon::today('America/Bogota')->year);

            if (!$isAdmin) {
                $professionalId = $user ? $user->id : null;
            }

            $baseQuery = Booking::with(['service', 'professional', 'customer'])
                ->whereMonth('scheduled_at', $month)
                ->whereYear('scheduled_at', $year)
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status));

            $totalBookings = (clone $baseQuery)->count();

            $monthlyRevenueQuery = Booking::where('payment_status', 'paid')
                ->whereMonth('payment_completed_at', $month)
                ->whereYear('payment_completed_at', $year)
                ->whereIn('status', ['pending', 'completed'])
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status));
            $monthlyRevenue = $monthlyRevenueQuery->sum('total_amount');

            $serviceRanking = (clone $baseQuery)
                ->select('service_id')
                ->groupBy('service_id')
                ->orderByRaw('COUNT(*) DESC')
                ->take(5)
                ->with('service')
                ->get()
                ->map(function ($booking) use ($professionalId, $status, $month, $year) {
                    $countQuery = Booking::where('service_id', $booking->service_id)
                        ->whereMonth('scheduled_at', $month)
                        ->whereYear('scheduled_at', $year)
                        ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                        ->when($status !== 'all', fn($q) => $q->where('status', $status));
                    return [
                        'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                        'count' => $countQuery->count(),
                    ];
                })->toArray();

            return response()->json([
                'metrics' => [
                    'total_bookings' => ['value' => $totalBookings],
                    'monthly_revenue' => [
                        'value' => $monthlyRevenue !== null ?
                            number_format((float) $monthlyRevenue, 0) : '0'
                    ],
                ],
                'service_ranking' => $serviceRanking,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMetricsAndRanking: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cargar métricas y ranking: ' . $e->getMessage()], 500);
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
        if ($request->has('month')) {
            $params['month'] = $request->input('month');
        }
        if ($request->has('year')) {
            $params['year'] = $request->input('year');
        }

        Log::info('Apply filter called', $params);

        return redirect()->route('platform.dashboard', $params);
    }

    public function refresh(Request $request)
    {
        Log::info('Refresh called', $request->only(['professional_id', 'status', 'selected_date', 'month', 'year']));

        return redirect()->route('platform.dashboard', $request->only(['professional_id', 'status', 'selected_date', 'month', 'year']));
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $roles = $user ? $user->roles()->pluck('slug')->toArray() : [];
            $isAdmin = in_array('admin', $roles);

            $professionalId = $request->input('professional_id', '');
            $status = $request->input('status', 'all');

            if (!$isAdmin) {
                $professionalId = $user ? $user->id : null;
            }

            $baseQuery = Booking::query();
            $revenueQuery = Booking::query();

            if ($request->has('month') && $request->has('year')) {
                $month = $request->input('month');
                $year = $request->input('year');
                
                $baseQuery->whereMonth('scheduled_at', $month)->whereYear('scheduled_at', $year);
                $revenueQuery->whereMonth('payment_completed_at', $month)->whereYear('payment_completed_at', $year);
            } else {
                $start = $request->input('start', Carbon::now('America/Bogota')->startOfMonth()->toDateString());
                $end = $request->input('end', Carbon::now('America/Bogota')->endOfMonth()->toDateString());

                $baseQuery->whereBetween('scheduled_at', [$start, $end]);
                $revenueQuery->whereBetween('payment_completed_at', [$start, $end]);
            }

            // Apply common filters
            $baseQuery->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                      ->when($status !== 'all', fn($q) => $q->where('status', $status));

            $revenueQuery->where('payment_status', 'paid')
                         ->whereIn('status', ['pending', 'completed'])
                         ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                         ->when($status !== 'all', fn($q) => $q->where('status', $status));

            $totalBookings = (clone $baseQuery)->count();
            $periodRevenue = $revenueQuery->sum('total_amount');

            $serviceRanking = (clone $baseQuery)
                ->select('service_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
                ->groupBy('service_id')
                ->orderBy('count', 'DESC')
                ->take(5)
                ->with('service')
                ->get()
                ->map(function ($item) {
                    return [
                        'service_name' => is_object($item->service) ? $item->service->name : 'Sin servicio',
                        'count' => $item->count,
                    ];
                })->toArray();

            $bookingsToday = Booking::whereDate('scheduled_at', Carbon::today('America/Bogota')->format('Y-m-d'))
                ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->count();

            $metrics = [
                'total_bookings' => ['value' => $totalBookings],
                'bookings_today' => ['value' => $bookingsToday],
                'monthly_revenue' => ['value' => $periodRevenue !== null ? number_format((float) $periodRevenue, 0) : '0'],
            ];

            $metricsHtml = view('_metrics', compact('metrics'))->render();
            $rankingHtml = view('_service_ranking', ['service_ranking' => $serviceRanking])->render();

            return response()->json([
                'metrics_html' => $metricsHtml,
                'ranking_html' => $rankingHtml,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDashboardData: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al cargar los datos del dashboard: ' . $e->getMessage()], 500);
        }
    }
}