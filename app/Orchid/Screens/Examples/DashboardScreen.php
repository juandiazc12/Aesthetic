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
    public function query(Request $request): iterable
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
                $result = [
                    'service_name' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'count' => Booking::where('service_id', $booking->service_id)
                        ->where('status', '!=', 'cancelled')
                        ->count(),
                ];
                Log::debug('Service ranking item', $result);
                return $result;
            })->toArray();

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
                $result = [
                    'id' => $booking->id,
                    'service' => is_object($booking->service) ? $booking->service->name : 'Sin servicio',
                    'professional' => is_object($booking->professional) ? $booking->professional->name : 'Sin profesional',
                    'customer' => is_object($booking->customer) ? $booking->customer->name : 'Sin cliente',
                    'scheduled_at' => $booking->scheduled_at instanceof \Carbon\Carbon ? $booking->scheduled_at->format('H:i') : 'Sin horario',
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
            'pending_bookings_count' => $pendingBookingsCount, // Added for navigation badge
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
                $startDate = $booking->scheduled_at instanceof \Carbon\Carbon
                    ? $booking->scheduled_at->toIso8601String()
                    : now()->toIso8601String();

                return [
                    'id' => $booking->id,
                    'title' => is_object($booking->service) ? $booking->service->name : 'Cita',
                    'start' => $startDate,
                    'url' => route('platform.bookings.edit', $booking),
                    'extendedProps' => [
                        'canCancel' => $booking->scheduled_at instanceof \Carbon\Carbon
                            ? Carbon::now()->lt($booking->scheduled_at->subHour())
                            : false,
                        'status' => $booking->status,
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
            if ($booking->scheduled_at instanceof \Carbon\Carbon && Carbon::now()->gte($booking->scheduled_at->subHour())) {
                return response()->json(['error' => 'No se puede cancelar menos de 1 hora antes.'], 403);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
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