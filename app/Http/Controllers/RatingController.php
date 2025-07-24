<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Intentando guardar calificación', [
            'request' => $request->all(),
            'cliente_autenticado' => Auth::guard('customer')->check(),
        ]);

        if (!Auth::guard('customer')->check()) { 
            Log::warning('Cliente no autenticado');
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $customer = Auth::guard('customer')->user();
        Log::info('Cliente autenticado', ['id' => $customer->id]);

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Log::info('Validación completada', ['validated' => $validated]);

        $booking = Booking::findOrFail($validated['booking_id']);
        Log::info('Reserva encontrada', ['booking_id' => $booking->id, 'customer_id' => $booking->customer_id]);

        // Validar que el que califica es el dueño de la reserva
        if ($booking->customer_id !== $customer->id) {
            Log::warning('Intento de calificación de reserva por cliente no autorizado', [
                'booking_customer_id' => $booking->customer_id,
                'auth_customer_id' => $customer->id
            ]);
            return response()->json(['error' => 'No autorizado para calificar esta reserva'], 403);
        }

        // Validar que no haya sido calificado antes
        if (Rating::where('booking_id', $booking->id)->exists()) {
            Log::warning('Reserva ya fue calificada previamente', ['booking_id' => $booking->id]);
            return response()->json(['error' => 'Ya has calificado esta reserva.'], 422);
        }

        $rating = Rating::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id, // profesional a quien se califica
            'customer_id' => $customer->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        Log::info('Calificación guardada exitosamente', [
            'rating_id' => $rating->id,
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'customer_id' => $customer->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Calificación registrada']);
    }
}
