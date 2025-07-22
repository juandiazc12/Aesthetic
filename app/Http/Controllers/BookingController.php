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
            'professional_id' => 'required|integer|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $professionalId = $request->professional_id;
        $date = $request->date;
        $serviceId = $request->service_id;

        Log::info('Fetching available slots', [
            'professional_id' => $professionalId,
            'date' => $date,
            'service_id' => $serviceId,
        ]);

        // Validar que el profesional existe
        $professional = User::find($professionalId);
        if (!$professional) {
            Log::error('Professional not found', ['professional_id' => $professionalId]);
            return response()->json(['error' => 'Profesional no encontrado'], 404);
        }

        // Obtener el servicio para conocer su duración
        $service = Service::find($serviceId);
        if (!$service) {
            Log::error('Service not found', ['service_id' => $serviceId]);
            return response()->json(['error' => 'Servicio no encontrado'], 404);
        }

        // CORRECCIÓN: Convertir explícitamente a entero
        $serviceDuration = (int) $service->duration; // en minutos

        Log::info('Service duration converted', [
            'original_duration' => $service->duration,
            'converted_duration' => $serviceDuration,
            'duration_type' => gettype($serviceDuration)
        ]);

        $requestDate = Carbon::createFromFormat('Y-m-d', $date, 'America/Bogota')->startOfDay();
        if ($requestDate->isPast() && !$requestDate->isToday()) {
            Log::warning('Attempt to fetch slots for past date', ['date' => $date]);
            return response()->json(['error' => 'No se pueden reservar fechas pasadas'], 400);
        }

        // Obtener todas las reservas del día para el profesional con sus horarios de fin
        $bookedSlots = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->whereDate('bookings.scheduled_at', $date)
            ->where('bookings.professional_id', $professionalId)
            ->whereNotIn('bookings.status', ['cancelled', 'completed'])
            ->select('bookings.scheduled_at', 'services.duration')
            ->get()
            ->map(function ($booking) {
                $startTime = Carbon::parse($booking->scheduled_at, 'America/Bogota');
                // CORRECCIÓN: Convertir también aquí a entero
                $duration = (int) $booking->duration;
                $endTime = $startTime->copy()->addMinutes($duration);
                return [
                    'start' => $startTime,
                    'end' => $endTime,
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $endTime->format('H:i'),
                    'duration' => $duration,
                ];
            });

        Log::debug('Booked slots for professional', [
            'professional_id' => $professionalId,
            'date' => $date,
            'booked_slots' => $bookedSlots->map(function($slot) {
                return [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'duration' => $slot['duration']
                ];
            })->toArray(),
        ]);

        $timeSlots = [
            'morning' => ['07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30'],
            'afternoon' => ['12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30'],
            'evening' => ['18:00', '18:30', '19:00', '19:30', '20:00', '20:30']
        ];

        $availableSlots = [];

        foreach ($timeSlots as $period => $slots) {
            $availableSlots[$period] = [];

            foreach ($slots as $slot) {
                $slotTime = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $slot, 'America/Bogota');
                
                // Verificar si el horario ya pasó (solo para hoy)
                if ($requestDate->isToday() && $slotTime->isPast()) {
                    continue;
                }

                // Calcular el horario de fin del nuevo servicio
                $newServiceEnd = $slotTime->copy()->addMinutes($serviceDuration);

                Log::debug('Checking slot availability', [
                    'slot_time' => $slot,
                    'slot_start' => $slotTime->format('H:i'),
                    'slot_end' => $newServiceEnd->format('H:i'),
                    'service_duration' => $serviceDuration
                ]);

                // Verificar si hay conflicto con reservas existentes
                $hasConflict = false;
                foreach ($bookedSlots as $bookedSlot) {
                    // El nuevo servicio tiene conflicto si:
                    // 1. Empieza antes de que termine una reserva existente Y termina después de que empiece esa reserva
                    if ($slotTime->lt($bookedSlot['end']) && $newServiceEnd->gt($bookedSlot['start'])) {
                        $hasConflict = true;
                        Log::debug('Conflict detected', [
                            'new_service_start' => $slotTime->format('H:i'),
                            'new_service_end' => $newServiceEnd->format('H:i'),
                            'existing_start' => $bookedSlot['start']->format('H:i'),
                            'existing_end' => $bookedSlot['end']->format('H:i'),
                        ]);
                        break;
                    }
                }

                if (!$hasConflict) {
                    $availableSlots[$period][] = $slot;
                    Log::debug('Slot available', ['slot' => $slot]);
                } else {
                    Log::debug('Slot not available due to conflict', ['slot' => $slot]);
                }
            }
        }

        Log::info('Available slots response', [
            'professional_id' => $professionalId,
            'date' => $date,
            'service_duration' => $serviceDuration,
            'available_slots_count' => [
                'morning' => count($availableSlots['morning']),
                'afternoon' => count($availableSlots['afternoon']),
                'evening' => count($availableSlots['evening'])
            ],
        ]);

        return response()->json($availableSlots);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation error in getAvailableSlots', [
            'errors' => $e->errors(),
            'professional_id' => $request->professional_id,
            'date' => $request->date,
        ]);
        return response()->json([
            'error' => 'Datos de entrada inválidos',
            'details' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error getting available slots: ' . $e->getMessage(), [
            'professional_id' => $request->professional_id,
            'date' => $request->date,
            'service_id' => $request->service_id,
            'trace' => $e->getTraceAsString(),
        ]);
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
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
            'payment_method' => 'required|string|in:efectivo,mercado_pago,transfer',
            'temp_id' => 'required|string',
        ]);

        if ($request->payment_method !== 'efectivo') {
            $request->validate([
                'payment_transaction_id' => 'required|string',
                'payment_amount' => 'required|numeric',
            ]);
        }

        $scheduledAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->scheduled_at, 'America/Bogota');
        if ($scheduledAt->isPast()) {
            return response()->json(['error' => 'La fecha y hora seleccionada ya pasó'], 400);
        }

        $existingBooking = Booking::where('professional_id', $request->professional_id)
            ->where('scheduled_at', $scheduledAt->toDateTimeString())
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
                'scheduled_at' => $scheduledAt->toDateTimeString(),
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
            'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
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

        $scheduledAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->scheduled_at, 'America/Bogota');
        if ($scheduledAt->isPast()) {
            return response()->json(['error' => 'La fecha y hora seleccionada ya pasó'], 400);
        }

        $existingBooking = Booking::where('professional_id', $request->professional_id)
            ->where('scheduled_at', $scheduledAt->toDateTimeString())
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
                'scheduled_at' => $scheduledAt->toDateTimeString(),
                'status' => 'pending',
                'total_amount' => $request->payment_method === 'efectivo' ? $service->price : $request->payment_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'efectivo' ? 'paid' : $request->payment_status,
                'payment_id' => $request->payment_method === 'efectivo' ? ($request->payment_transaction_id ?? 'cash_' . Carbon::now('America/Bogota')->timestamp) : $request->payment_transaction_id,
                'payment_completed_at' => $request->payment_method === 'efectivo' ? Carbon::now('America/Bogota')->toDateTimeString() : ($request->payment_status === 'paid' ? Carbon::now('America/Bogota')->toDateTimeString() : null),
            ]);

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
                    'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->toDateTimeString(),
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
                $bookingData['scheduled_at'] = Carbon::parse($bookingData['scheduled_at'], 'America/Bogota')->toDateTimeString();
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
                'confirmed_at' => Carbon::now('America/Bogota')->toDateTimeString(),
            ]);

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
                    'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->toDateTimeString(),
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
    try {
        Log::info('Fetching bookings for customer', [
            'customer_id' => Auth::guard('customer')->id(),
        ]);

        $bookings = Booking::with(['service', 'professional'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => [
                        'id' => $booking->service ? $booking->service->id : null,
                        'name' => $booking->service ? $booking->service->name : 'Desconocido',
                        'duration' => $booking->service ? $booking->service->duration : null,
                        'image' => $booking->service ? $booking->service->image : null,
                        'price' => $booking->service ? $booking->service->price : null,
                    ],
                    'professional' => [
                        'id' => $booking->professional ? $booking->professional->id : null,
                        'name' => $booking->professional ? $booking->professional->name : 'Desconocido',
                        'email' => $booking->professional ? $booking->professional->email : null,
                        'photo' => $booking->professional ? $booking->professional->photo : null,
                    ],
                    'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->toDateTimeString(),
                    'scheduled_date' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('d/m/Y'),
                    'scheduled_time' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i'),
                    'scheduled_day' => $this->getDayNameInSpanish(Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('l')),
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'status' => $booking->status,
                    'status_spanish' => $booking->statusSpanish,
                    'created_at' => Carbon::parse($booking->created_at, 'America/Bogota')->toDateTimeString(),
                    'completed_at' => $booking->completed_at ? Carbon::parse($booking->completed_at, 'America/Bogota')->toDateTimeString() : null,
                ];
            });

        return Inertia::render('Booking/BookingList', [
            'bookings' => $bookings,
            'customer' => Auth::guard('customer')->user(),
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching bookings: ' . $e->getMessage(), [
            'customer_id' => Auth::guard('customer')->id(),
            'trace' => $e->getTraceAsString(),
        ]);
        return Inertia::render('Booking/BookingList', [
            'bookings' => [],
            'customer' => Auth::guard('customer')->user(),
            'error' => 'Error al cargar las reservas: ' . $e->getMessage(),
        ]);
    }
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
                return redirect()->route('booking.BookingList')->with('error', 'No se puede cancelar la reserva menos de 1 hora antes del servicio');
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => Carbon::now('America/Bogota')->toDateTimeString(),
            ]);

            $booking->customer->notify(new \App\Notifications\BookingCancelled($booking, 'La cita fue cancelada por el cliente.'));

            Log::info('Booking cancelled', ['booking_id' => $booking->id]);

            return redirect()->route('booking.BookingList')->with('success', 'Reserva cancelada exitosamente');

        } catch (\Exception $e) {
            Log::error('Error cancelling booking: ' . $e->getMessage());
            return redirect()->route('booking.BookingList')->with('error', 'Error al cancelar la reserva: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $booking = Booking::with(['service', 'professional'])
                ->where('id', $id)
                ->where('customer_id', Auth::guard('customer')->id())
                ->firstOrFail();

            Log::info('Attempting to update booking', [
                'booking_id' => $id,
                'customer_id' => Auth::guard('customer')->id(),
                'request_data' => $request->all(),
            ]);

            $scheduledAt = Carbon::parse($booking->scheduled_at, 'America/Bogota');
            $now = Carbon::now('America/Bogota');
            $oneHourBefore = $scheduledAt->copy()->subHour();

            if ($now->greaterThanOrEqualTo($oneHourBefore)) {
                Log::warning('Attempt to edit booking too late', [
                    'booking_id' => $booking->id,
                    'scheduled_at' => $booking->scheduled_at,
                    'now' => $now,
                ]);
                return Inertia::render('Booking/BookingList', [
                    'bookings' => $this->getMappedBookings(),
                    'customer' => Auth::guard('customer')->user(),
                    'error' => 'No se puede editar la reserva menos de 1 hora antes del servicio',
                ]);
            }

            $request->validate([
                'service_id' => 'required|exists:services,id',
                'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
            ]);

            // Verificar si el profesional ofrece el servicio
            $service = Service::findOrFail($request->service_id);
            $serviceList = ServiceList::where('name', $service->name)->first();
            if ($serviceList) {
                $hasService = $serviceList->servicesList()
                    ->where('users.id', $booking->professional_id)
                    ->exists();
                if (!$hasService) {
                    Log::warning('Professional does not offer this service', [
                        'booking_id' => $booking->id,
                        'service_id' => $request->service_id,
                        'professional_id' => $booking->professional_id,
                    ]);
                    return Inertia::render('Booking/BookingList', [
                        'bookings' => $this->getMappedBookings(),
                        'customer' => Auth::guard('customer')->user(),
                        'error' => 'El profesional asignado no ofrece este servicio',
                    ]);
                }
            }

            $newScheduledAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->scheduled_at, 'America/Bogota');
            if ($newScheduledAt->isPast()) {
                Log::warning('Attempt to set past date for booking', [
                    'booking_id' => $booking->id,
                    'new_scheduled_at' => $request->scheduled_at,
                ]);
                return Inertia::render('Booking/BookingList', [
                    'bookings' => $this->getMappedBookings(),
                    'customer' => Auth::guard('customer')->user(),
                    'error' => 'La fecha y hora seleccionada ya pasó',
                ]);
            }

            $existingBooking = Booking::where('professional_id', $booking->professional_id)
                ->where('scheduled_at', $newScheduledAt->toDateTimeString())
                ->whereNotIn('status', ['cancelled', 'completed'])
                ->where('id', '!=', $id)
                ->first();

            if ($existingBooking) {
                Log::warning('Selected time slot is already booked', [
                    'booking_id' => $booking->id,
                    'new_scheduled_at' => $newScheduledAt,
                    'existing_booking_id' => $existingBooking->id,
                ]);
                return Inertia::render('Booking/BookingList', [
                    'bookings' => $this->getMappedBookings(),
                    'customer' => Auth::guard('customer')->user(),
                    'error' => 'El horario ya está ocupado',
                ]);
            }

            $booking->update([
                'service_id' => $request->service_id,
                'scheduled_at' => $newScheduledAt->toDateTimeString(),
                'status' => 'pending',
            ]);

            // Enviar notificación de edición
            try {
                $booking->customer->notify(new \App\Notifications\BookingEdited($booking));
            } catch (\Exception $e) {
                Log::warning('Failed to send BookingEdited notification: ' . $e->getMessage());
            }

            Log::info('Booking updated successfully', [
                'booking_id' => $booking->id,
                'service_id' => $request->service_id,
                'scheduled_at' => $newScheduledAt->toDateTimeString(),
            ]);

            return Inertia::render('Booking/BookingList', [
                'bookings' => $this->getMappedBookings(),
                'customer' => Auth::guard('customer')->user(),
                'success' => 'Reserva actualizada exitosamente',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in update booking', [
                'booking_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating booking: ' . $e->getMessage(), [
                'booking_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Inertia::render('Booking/BookingList', [
                'bookings' => $this->getMappedBookings(),
                'customer' => Auth::guard('customer')->user(),
                'error' => 'Error al actualizar la reserva: ' . $e->getMessage(),
            ]);
        }
    }

    // Método auxiliar para obtener las reservas mapeadas
    private function getMappedBookings()
    {
        return Booking::with(['service', 'professional'])
            ->where('customer_id', Auth::guard('customer')->id())
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service' => [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name,
                        'duration' => $booking->service->duration,
                        'image' => $booking->service->image,
                        'price' => $booking->service->price,
                    ],
                    'professional' => [
                        'id' => $booking->professional ? $booking->professional->id : 0,
                        'name' => $booking->professional ? $booking->professional->name : 'Desconocido',
                        'email' => $booking->professional ? $booking->professional->email : null,
                        'photo' => $booking->professional ? $booking->professional->photo : null,
                    ],
                    'scheduled_at' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->toDateTimeString(),
                    'scheduled_date' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('d/m/Y'),
                    'scheduled_time' => Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('H:i'),
                    'scheduled_day' => $this->getDayNameInSpanish(Carbon::parse($booking->scheduled_at, 'America/Bogota')->format('l')),
                    'total_amount' => $booking->total_amount,
                    'payment_method' => $booking->payment_method,
                    'payment_status' => $booking->payment_status,
                    'status' => $booking->status,
                    'status_spanish' => $booking->statusSpanish,
                    'created_at' => Carbon::parse($booking->created_at, 'America/Bogota')->toDateTimeString(),
                    'completed_at' => $booking->completed_at ? Carbon::parse($booking->completed_at, 'America/Bogota')->toDateTimeString() : null,
                ];
            });
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
                ->whereBetween('scheduled_at', [$startOfWeek->toDateTimeString(), $endOfWeek->toDateTimeString()])
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