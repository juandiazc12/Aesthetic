<?php
namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Booking;
use App\Models\ServiceList;
use App\Models\User;
use App\Notifications\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class BookingController extends Controller
    {
        public function show($serviceId = null)
        {
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

            $professionals = collect();
            if ($serviceId) {
                $service = Service::findOrFail($serviceId);
                $serviceList = ServiceList::where('name', $service->name)->first();
                if ($serviceList) {
                    $professionals = $serviceList->servicesList()
                        ->whereHas('roles', function ($query) {
                            $query->where('slug', 'profesional');
                        })
                        ->select('users.id', 'users.name', 'users.email', 'users.photo')
                        ->get();
                }
            } else {
                $professionals = User::whereHas('roles', function ($query) {
                    $query->where('slug', 'profesional');
                })->select('id', 'name', 'email', 'photo')->get();
            }

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
                $serviceList = ServiceList::where('name', $service->name)->first();
                $professionals = collect();

                if ($serviceList) {
                    $professionals = $serviceList->servicesList()
                        ->whereHas('roles', function ($query) {
                            $query->where('slug', 'profesional');
                        })
                        ->select('users.id', 'users.name', 'users.email', 'users.photo')
                        ->get();
                }

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

                $requestDate = Carbon::parse($date, 'America/Bogota');
                if ($requestDate->isPast() && !$requestDate->isToday()) {
                    return response()->json(['error' => 'No se pueden reservar fechas pasadas'], 400);
                }

                $bookedTimes = Booking::whereDate('scheduled_at', $date)
                    ->where('professional_id', $professionalId)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->pluck('scheduled_at')
                    ->map(function ($item) {
                        return Carbon::parse($item, 'America/Bogota')->format('H:i');
                    })
                    ->toArray();

                $timeSlots = [
                    'morning' => ['07:00', '08:00', '09:00', '10:00', '11:00'],
                    'afternoon' => ['12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
                    'evening' => ['18:00', '19:00', '20:00', '21:00', '22:00']
                ];

                $availableSlots = [];
                foreach ($timeSlots as $period => $slots) {
                    $availableSlots[$period] = [];
                    foreach ($slots as $slot) {
                        if ($requestDate->isToday()) {
                            $slotTime = Carbon::parse($date . ' ' . $slot, 'America/Bogota');
                            if ($slotTime->isPast()) {
                                continue;
                            }
                        }
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
                'payment_method' => 'required|string|in:efectivo,mercado_pago,transfer',
                'temp_id' => 'required|string',
            ]);

            if ($request->payment_method !== 'efectivo') {
                $request->validate([
                    'payment_transaction_id' => 'required|string',
                    'payment_amount' => 'required|numeric',
                ]);
            }

            $scheduledAt = Carbon::parse($request->scheduled_at, 'America/Bogota');

            $existingBooking = Booking::where('professional_id', $request->professional_id)
                ->where('scheduled_at', $scheduledAt)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->first();

            if ($existingBooking) {
                return response()->json(['error' => 'El horario ya está ocupado'], 400);
            }

            try {
                $service = Service::findOrFail($request->service_id);
                $professional = User::findOrFail($request->professional_id);
                $customer = Auth::guard('customer')->user();

                $bookingData = [
                    'id' => $request->temp_id,
                    'customer_id' => $customer->id,
                    'service_id' => $request->service_id,
                    'professional_id' => $request->professional_id,
                    'scheduled_at' => $scheduledAt,
                    'payment_method' => $request->payment_method,
                    'total_amount' => $request->payment_method === 'efectivo' ? $service->price : $request->payment_amount,
                    'payment_id' => $request->payment_method === 'efectivo' ? ($request->payment_transaction_id ?? 'cash_' . Carbon::now('America/Bogota')->timestamp) : $request->payment_transaction_id,
                    'payment_status' => $request->payment_method === 'efectivo' ? 'paid' : ($request->payment_status ?? 'pending'),
                    'service' => [
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                    ],
                    'professional' => [
                        'name' => $professional->name,
                    ],
                    'is_confirmed' => false,
                    'temp_id' => $request->temp_id,
                ];

                session(['booking_data_' . $request->temp_id => $bookingData]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('booking.confirmation', ['booking' => $request->temp_id]),
                    'booking_data' => $bookingData,
                ]);

            } catch (\Exception $e) {
                Log::error('Error validando reserva: ' . $e->getMessage());
                return response()->json(['error' => 'Error al validar la reserva: ' . $e->getMessage()], 500);
            }
        }

        public function confirmStore(Request $request)
        {
            if (!Auth::guard('customer')->check()) {
                return response()->json(['error' => 'No autorizado'], 401);
            }

            $request->validate([
                'service_id' => 'required|exists:services,id',
                'professional_id' => 'required|exists:users,id',
                'scheduled_at' => 'required|date|after:now',
                'payment_method' => 'required|string|in:efectivo,mercado_pago,transfer',
                'temp_id' => 'required|string',
            ]);

            if ($request->payment_method !== 'efectivo') {
                $request->validate([
                    'payment_transaction_id' => 'required|string',
                    'payment_amount' => 'required|numeric',
                    'payment_status' => 'required|string|in:pending,paid,failed',
                ]);
            }

            $scheduledAt = Carbon::parse($request->scheduled_at, 'America/Bogota');

            $existingBooking = Booking::where('professional_id', $request->professional_id)
                ->where('scheduled_at', $scheduledAt)
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->first();

            if ($existingBooking) {
                return response()->json(['error' => 'El horario ya está ocupado'], 400);
            }

            try {
                $service = Service::findOrFail($request->service_id);
                $professional = User::findOrFail($request->professional_id);
                $customer = Auth::guard('customer')->user();

                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'service_id' => $request->service_id,
                    'professional_id' => $request->professional_id,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'pending',
                    'total_amount' => $request->payment_method === 'efectivo' ? $service->price : $request->payment_amount,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_method === 'efectivo' ? 'paid' : $request->payment_status,
                    'payment_id' => $request->payment_method === 'efectivo' ? ($request->payment_transaction_id ?? 'cash_' . Carbon::now('America/Bogota')->timestamp) : $request->payment_transaction_id,
                    'payment_completed_at' => $request->payment_method === 'efectivo' ? Carbon::now('America/Bogota') : ($request->payment_status === 'paid' ? Carbon::now('America/Bogota') : null),
                ]);

                // Enviar notificación por correo electrónico con integración de calendario
                $customer->notify(new BookingConfirmation($booking));

                session()->forget('booking_data_' . $request->temp_id);

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('booking.confirmation', ['booking' => $booking->id]),
                    'booking' => [
                        'id' => $booking->id,
                        'scheduled_at' => $booking->scheduled_at,
                        'service' => [
                            'name' => $service->name,
                            'price' => $service->price,
                            'duration' => $service->duration,
                        ],
                        'professional' => [
                            'name' => $professional->name,
                        ],
                        'payment_method' => $booking->payment_method,
                        'payment_status' => $booking->payment_status,
                        'total_amount' => $booking->total_amount,
                        'is_confirmed' => true,
                    ],
                ]);

            } catch (\Exception $e) {
                Log::error('Error creando reserva: ' . $e->getMessage());
                return response()->json(['error' => 'Error al crear la reserva: ' . $e->getMessage()], 500);
            }
        }

        public function confirmation($bookingId)
        {
            try {
                $bookingData = session('booking_data_' . $bookingId);

                if (!$bookingData) {
                    $booking = Booking::with(['service', 'professional', 'customer'])
                        ->where('id', $bookingId)
                        ->where('customer_id', Auth::guard('customer')->id())
                        ->firstOrFail();

                    $bookingData = [
                        'id' => $booking->id,
                        'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota'),
                        'service' => [
                            'name' => $booking->service->name,
                            'price' => $booking->service->price,
                            'duration' => $booking->service->duration,
                        ],
                        'professional' => [
                            'name' => $booking->professional->name,
                        ],
                        'payment_method' => $booking->payment_method,
                        'payment_status' => $booking->payment_status,
                        'total_amount' => $booking->total_amount,
                        'is_confirmed' => true,
                    ];
                } else {
                    $bookingData['is_confirmed'] = false;
                    $bookingData['total_amount'] = $bookingData['total_amount'] ?? $bookingData['service']['price'];
                    $bookingData['scheduled_at'] = Carbon::parse($bookingData['scheduled_at'], 'America/Bogota');
                }

                Log::info('Mostrando confirmación de reserva', [
                    'booking_id' => $bookingId,
                    'is_confirmed' => $bookingData['is_confirmed'],
                    'payment_method' => $bookingData['payment_method'],
                ]);

                return Inertia::render('Booking/BookingConfirmation', [
                    'booking' => $bookingData,
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

                if (!in_array($booking->status, ['pending', 'confirmed'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'La reserva no puede ser confirmada desde su estado actual: ' . $booking->status
                    ], 400);
                }

                $booking->update([
                    'status' => 'confirmed',
                    'confirmed_at' => Carbon::now('America/Bogota'),
                ]);

                // Enviar notificación por correo electrónico con integración de calendario si no se ha enviado antes
                if ($booking->wasChanged('status') && $booking->status === 'confirmed') {
                    $booking->customer->notify(new BookingConfirmation($booking));
                }

                Log::info('Reserva confirmada exitosamente', [
                    'booking_id' => $booking->id,
                    'new_status' => $booking->status,
                    'confirmed_at' => $booking->confirmed_at
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reserva confirmada exitosamente',
                    'booking' => [
                        'id' => $booking->id,
                        'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota'),
                        'service' => [
                            'name' => $booking->service->name,
                            'price' => $booking->service->price,
                            'duration' => $booking->service->duration,
                        ],
                        'professional' => [
                            'name' => $booking->professional->name,
                        ],
                        'payment_method' => $booking->payment_method,
                        'payment_status' => $booking->payment_status,
                        'total_amount' => $booking->total_amount,
                        'is_confirmed' => true,
                    ],
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
                    'image' => $booking->service->image, // Agregar imagen del servicio
                ],
                'professional' => [
                    'name' => $booking->professional->name,
                    'email' => $booking->professional->email, // Agregar email del profesional
                    'photo' => $booking->professional->photo,
                ],
                'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('Y-m-d H:i:s'),
                'scheduled_date' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('d/m/Y'),
                'scheduled_time' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i'),
                'scheduled_day' => $this->getDayNameInSpanish(Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('l')),
                'total_amount' => $booking->total_amount,
                'payment_method' => $booking->payment_method,
                'payment_status' => $booking->payment_status,
                'status' => $booking->status,
                'status_spanish' => $booking->statusSpanish,
                'created_at' => Carbon::parse($booking->created_at, 'America/Bogota')->format('Y-m-d H:i:s'),
                'completed_at' => $booking->completed_at ? Carbon::parse($booking->completed_at, 'America/Bogota')->format('Y-m-d H:i:s') : null,
            ];
        });

    return Inertia::render('Booking/BookingList', [ // Cambiar a BookingList con B mayúscula
        'bookings' => $bookings,
        'customer' => Auth::guard('customer')->user(), // Agregar customer si lo necesitas
        'professionals' => [] 
    ]);
}

        public function destroy($id)
        {
            try {
                $booking = Booking::where('id', $id)
                    ->where('customer_id', Auth::guard('customer')->id())
                    ->firstOrFail();

                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                $now = Carbon::now('America/Bogota');
                $oneHourBefore = $scheduledAt->copy()->subHour();

                if ($now->greaterThanOrEqualTo($oneHourBefore)) {
                    Log::warning('Attempt to cancel booking too late', [
                        'booking_id' => $booking->id,
                        'scheduled_at' => $booking->scheduled_at,
                        'now' => $now,
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'No se puede cancelar la reserva menos de 1 hora antes del servicio',
                    ], 403);
                }

                Log::info('Attempting to cancel booking', [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'customer_id' => Auth::guard('customer')->id(),
                ]);

                $booking->update([
                    'status' => 'cancelled',
                    'cancelled_at' => Carbon::now('America/Bogota'),
                ]);

                Log::info('Booking cancelled', ['booking_id' => $booking->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reserva cancelada exitosamente',
                ]);
            } catch (\Exception $e) {
                Log::error('Error cancelling booking: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al cancelar la reserva: ' . $e->getMessage(),
                ], 500);
            }
        }

        public function update(Request $request, $id)
        {
            try {
                $booking = Booking::where('id', $id)
                    ->where('customer_id', Auth::guard('customer')->id())
                    ->firstOrFail();

                $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                $now = Carbon::now('America/Bogota');
                $oneHourBefore = $scheduledAt->copy()->subHour();

                if ($now->greaterThanOrEqualTo($oneHourBefore)) {
                    Log::warning('Attempt to edit booking too late', [
                        'booking_id' => $booking->id,
                        'scheduled_at' => $booking->scheduled_at,
                        'now' => $now,
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'No se puede editar la reserva menos de 1 hora antes del servicio',
                    ], 403);
                }

                $request->validate([
                    'service_id' => 'required|exists:services,id',
                    'professional_id' => 'required|exists:users,id',
                    'scheduled_at' => 'required|date|after:now',
                ]);

                $newScheduledAt = Carbon::parse($request->scheduled_at, 'America/Bogota');

                $existingBooking = Booking::where('professional_id', $request->professional_id)
                    ->where('scheduled_at', $newScheduledAt)
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingBooking) {
                    return response()->json(['error' => 'El horario ya está ocupado'], 400);
                }

                $booking->update([
                    'service_id' => $request->service_id,
                    'professional_id' => $request->professional_id,
                    'scheduled_at' => $newScheduledAt,
                    'status' => 'pending',
                ]);

                Log::info('Booking updated', ['booking_id' => $booking->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Reserva actualizada exitosamente',
                    'booking' => [
                        'id' => $booking->id,
                        'service' => [
                            'name' => $booking->service->name,
                            'duration' => $booking->service->duration,
                            'image' => $booking->service->image ? \Storage::url($booking->service->image) : null,
                        ],
                        'professional' => [
                            'name' => $booking->professional->name,
                            'photo' => $booking->professional->photo ? \Storage::url($booking->professional->photo) : null,
                        ],
                        'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota'),
                        'status' => $booking->status,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Error updating booking: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Error al actualizar la reserva: ' . $e->getMessage(),
                ], 500);
            }
        }

        public function getAvailableDates(Request $request)
        {
            try {
                $request->validate([
                    'professional_id' => 'required|exists:users,id',
                    'week_offset' => 'integer|min:0|max:12',
                ]);

                $professionalId = $request->professional_id;
                $weekOffset = $request->week_offset ?? 0;

                $startOfWeek = Carbon::now('America/Bogota')->startOfWeek()->addWeeks($weekOffset);
                $endOfWeek = $startOfWeek->copy()->endOfWeek();

                $bookedDays = Booking::where('professional_id', $professionalId)
                    ->whereBetween('scheduled_at', [$startOfWeek, $endOfWeek])
                    ->whereNotIn('status', ['cancelled', 'completed'])
                    ->get()
                    ->groupBy(function ($booking) {
                        return Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('Y-m-d');
                    })
                    ->map(function ($dayBookings) {
                        return $dayBookings->count();
                    });

                $weekDays = [];
                $currentDate = $startOfWeek->copy();
                $maxSlotsPerDay = 15;
                $today = Carbon::now('America/Bogota')->format('Y-m-d');

                for ($i = 0; $i < 7; $i++) {
                    $dateKey = $currentDate->format('Y-m-d');
                    $bookedCount = $bookedDays->get($dateKey, 0);
                    $isPast = $currentDate->isPast() && !$currentDate->isToday();
                    $isToday = $currentDate->format('Y-m-d') === $today;

                    $weekDays[] = [
                        'date' => $dateKey,
                        'day_name' => $currentDate->format('l'),
                        'day_name_es' => $this->getDayNameInSpanish($currentDate->format('l')),
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
