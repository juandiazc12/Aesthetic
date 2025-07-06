<?php
namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function show($serviceId = null)
    {
        if ($serviceId) {
            $service = Service::findOrFail($serviceId);
            $professionals = $service->getActiveProfessionals();
        } else {
            $professionals = User::whereHas('roles', function ($query) {
                $query->where('slug', 'profesional');
            })->select('id', 'name', 'email', 'photo')->get();
        }
        $services = Service::where('status', 'active')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration' => $service->duration,
                    'status' => $service->status,
                ];
            });
        return Inertia::render('Booking/booking', [
            'initialServices' => $services,
            'professionals' => $professionals,
            'selectedService' => $serviceId ? $service : null,
        ]);
    }

    public function getProfessionalsByService($serviceId)
    {
        try {
            $service = Service::findOrFail($serviceId);
            $professionals = $service->getActiveProfessionals();
            
            return response()->json($professionals);
        } catch (\Exception $e) {
            Log::error('Error getting professionals by service: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener profesionales'], 500);
        }
    }

    public function getAvailableSlots(Request $request)
    {
        try {
            $request->validate([
                'professional_id' => 'required|exists:users,id',
                'date' => 'required|date'
            ]);
            $professionalId = $request->professional_id;
            $date = $request->date;
            
            // Verificar que la fecha no sea pasada
            $requestDate = Carbon::parse($date);
            if ($requestDate->isPast() && !$requestDate->isToday()) {
                return response()->json(['error' => 'No se pueden reservar fechas pasadas'], 400);
            }
            
            // Obtener citas ya reservadas para ese profesional en esa fecha
            $bookedTimes = Booking::whereDate('scheduled_at', $date)
                ->where('professional_id', $professionalId)
                ->whereNotIn('status', ['cancelled']) // Excluir canceladas
                ->pluck('scheduled_at')
                ->map(function ($item) {
                    return Carbon::parse($item)->format('H:i');
                })
                ->toArray();
            
            // Horarios base agrupados por períodos
            $timeSlots = [
                'morning' => ['07:00', '08:00', '09:00', '10:00', '11:00'],
                'afternoon' => ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
                'evening' => ['18:00', '19:00', '20:00', '21:00', '22:00']
            ];
            
            $availableSlots = [];
            foreach ($timeSlots as $period => $slots) {
                $availableSlots[$period] = [];
                
                foreach ($slots as $slot) {
                    // Si es hoy, verificar que no haya pasado la hora
                    if ($requestDate->isToday()) {
                        $slotTime = Carbon::parse($date . ' ' . $slot);
                        if ($slotTime->isPast()) {
                            continue;
                        }
                    }
                    
                    // Verificar que no esté ocupado
                    if (!in_array($slot, $bookedTimes)) {
                        $availableSlots[$period][] = $slot;
                    }
                }
            }
            
            return response()->json($availableSlots);
        } catch (\Exception $e) {
            Log::error('Error getting available slots: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener horarios disponibles'], 500);
        }
    }

    public function store(Request $request)
{
    if (!Auth::guard('customer')->check()) {
        return response()->json(['error' => 'No autorizado'], 401);
    }
    
    $request->validate([
        'service_id' => 'required|exists:services,id',
        'professional_id' => 'required|exists:users,id',
        'scheduled_at' => 'required|date|after:now',
        'payment_method' => 'required|in:cash,card,transfer',
        'payment_amount' => 'required|numeric|min:0',
    ]);
    
    // Verificar que el horario esté disponible
    $existingBooking = Booking::where('professional_id', $request->professional_id)
        ->where('scheduled_at', $request->scheduled_at)
        ->whereNotIn('status', ['cancelled'])
        ->first();
    
    if ($existingBooking) {
        return response()->json(['error' => 'El horario ya está ocupado'], 400);
    }
    
    try {
        // Determinar el estado inicial basado en el método de pago
        $initialStatus = $request->payment_method === 'cash' ? 'confirmed' : 'pending';
        $paymentStatus = $request->payment_method === 'cash' ? 'completed' : 'pending';
        
        // Crear la reserva
        $booking = Booking::create([
            'customer_id' => Auth::guard('customer')->id(),
            'service_id' => $request->service_id,
            'professional_id' => $request->professional_id,
            'scheduled_at' => $request->scheduled_at,
            'status' => $initialStatus,
            'total_amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'payment_status' => $paymentStatus,
            'payment_id' => $request->payment_method === 'cash' ? 'cash_' . uniqid() : null,
            'confirmed_at' => $request->payment_method === 'cash' ? now() : null,
        ]);
        
        // Cargar las relaciones para la respuesta
        $booking->load('service', 'professional', 'customer');
        
        Log::info('Reserva creada exitosamente', [
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'status' => $booking->status,
            'payment_method' => $booking->payment_method,
            'payment_status' => $booking->payment_status
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'booking' => $booking,
            'redirect_url' => route('booking.confirmation', ['booking' => $booking->id])
        ], 201);
        
    } catch (\Exception $e) {
        Log::error('Error creating booking: ' . $e->getMessage());
        return response()->json(['error' => 'Error al crear la reserva'], 500);
    }
}

    public function confirmation($bookingId)
    {
        try {
            $booking = Booking::with(['service', 'professional', 'customer'])
                ->where('id', $bookingId)
                ->where('customer_id', Auth::guard('customer')->id())
                ->firstOrFail();
            
            Log::info('Mostrando confirmación de reserva', [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status
            ]);
            
            return Inertia::render('Booking/Confirmation', [
                'booking' => [
                    'id' => $booking->id,
                    'service' => [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name,
                        'description' => $booking->service->description,
                        'price' => $booking->service->price,
                        'duration' => $booking->service->duration,
                    ],
                    'professional' => [
                        'name' => $booking->professional->name,
                        'email' => $booking->professional->email,
                        'photo' => $booking->professional->photo,
                    ],
                    'customer' => [
                        'name' => $booking->customer->name,
                        'email' => $booking->customer->email,
                    ],
                    'scheduled_at' => $booking->scheduled_at,
                    'scheduled_date' => Carbon::parse($booking->scheduled_at)->format('d/m/Y'),
                    'scheduled_time' => Carbon::parse($booking->scheduled_at)->format('H:i'),
                    'scheduled_day' => Carbon::parse($booking->scheduled_at)->format('l'),
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'confirmed_at' => $booking->confirmed_at,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error mostrando confirmación: ' . $e->getMessage());
            return redirect()->route('booking.list')->with('error', 'Reserva no encontrada');
        }
    }

    public function confirmBooking(Request $request, $bookingId)
    {
        try {
            $booking = Booking::where('id', $bookingId)
                ->where('customer_id', Auth::guard('customer')->id())
                ->firstOrFail();
            
            Log::info('Intentando confirmar reserva', [
                'booking_id' => $booking->id,
                'current_status' => $booking->status,
                'payment_method' => $booking->payment_method
            ]);
            
            // Verificar que la reserva pueda ser confirmada
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'La reserva no puede ser confirmada desde su estado actual: ' . $booking->status
                ], 400);
            }
            
            // Confirmar la reserva
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
            
            Log::info('Reserva confirmada exitosamente', [
                'booking_id' => $booking->id,
                'new_status' => $booking->status,
                'confirmed_at' => $booking->confirmed_at
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reserva confirmada exitosamente',
                'booking' => $booking,
                'redirect_url' => route('booking.list')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error confirmando reserva: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al confirmar la reserva'
            ], 500);
        }
    }

    public function list()
    {
        $bookings = Booking::with(['service', 'professional'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => [
                        'name' => $booking->service->name,
                        'duration' => $booking->service->duration,
                    ],
                    'professional' => [
                        'name' => $booking->professional->name,
                        'photo' => $booking->professional->photo,
                    ],
                    'scheduled_at' => $booking->scheduled_at,
                    'scheduled_date' => Carbon::parse($booking->scheduled_at)->format('d/m/Y'),
                    'scheduled_time' => Carbon::parse($booking->scheduled_at)->format('H:i'),
                    'scheduled_day' => Carbon::parse($booking->scheduled_at)->format('l'),
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                ];
            });
        
        return Inertia::render('Booking/BookingList', [
            'bookings' => $bookings
        ]);
    }

    // Método actualizado para obtener fechas disponibles por semana
    public function getAvailableDates(Request $request)
    {
        try {
            $request->validate([
                'professional_id' => 'required|exists:users,id',
                'week_offset' => 'integer|min:0|max:12', // Permitir hasta 12 semanas adelante
            ]);
            
            $professionalId = $request->professional_id;
            $weekOffset = $request->week_offset ?? 0;
            
            // Calcular la fecha de inicio de la semana
            $startOfWeek = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            
            // Obtener días con reservas de toda la semana
            $bookedDays = Booking::where('professional_id', $professionalId)
                ->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek])
                ->whereNotIn('status', ['cancelled'])
                ->get()
                ->groupBy(function ($booking) {
                    return Carbon::parse($booking->scheduled_at)->format('Y-m-d');
                })
                ->map(function ($dayBookings) {
                    return $dayBookings->count();
                });
            
            // Generar días de la semana
            $weekDays = [];
            $currentDate = $startOfWeek->copy();
            $maxSlotsPerDay = 15; // Máximo de slots por día
            
            // Obtener el día actual para comparaciones
            $today = Carbon::now()->format('Y-m-d');
            
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $currentDate->format('Y-m-d');
                $bookedCount = $bookedDays->get($dateKey, 0);
                $isPast = $currentDate->isPast() && !$currentDate->isToday();
                $isToday = $currentDate->format('Y-m-d') === $today;
                
                $weekDays[] = [
                    'date' => $dateKey,
                    'day_name' => $currentDate->format('l'), // Nombre del día en inglés
                    'day_name_es' => $this->getDayNameInSpanish($currentDate->format('l')), // Nombre en español
                    'day_number' => $currentDate->day,
                    'month' => $currentDate->month,
                    'year' => $currentDate->year,
                    'formatted_date' => $currentDate->format('d/m/Y'),
                    'is_available' => !$isPast && $bookedCount < $maxSlotsPerDay,
                    'is_today' => $isToday,
                    'is_past' => $isPast,
                    'booked_count' => $bookedCount,
                    'slots_available' => max(0, $maxSlotsPerDay - $bookedCount),
                ];
                
                $currentDate->addDay();
            }
            
            return response()->json([
                'week_days' => $weekDays,
                'week_start' => $startOfWeek->format('Y-m-d'),
                'week_end' => $endOfWeek->format('Y-m-d'),
                'week_offset' => $weekOffset,
                'professional_id' => $professionalId,
                'week_title' => $this->getWeekTitle($startOfWeek, $endOfWeek),
                'can_go_previous' => $weekOffset > 0,
                'can_go_next' => $weekOffset < 12,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting available dates: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener fechas disponibles'], 500);
        }
    }

    // Método auxiliar para obtener nombres de días en español
    private function getDayNameInSpanish($dayName)
    {
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo',
        ];
        
        return $days[$dayName] ?? $dayName;
    }

    // Método auxiliar para obtener el título de la semana
    private function getWeekTitle($startOfWeek, $endOfWeek)
    {
        $startMonth = $startOfWeek->format('M');
        $endMonth = $endOfWeek->format('M');
        
        if ($startMonth === $endMonth) {
            return $startOfWeek->format('d') . ' - ' . $endOfWeek->format('d M Y');
        } else {
            return $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');
        }
    }
}