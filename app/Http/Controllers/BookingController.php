<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use App\Models\User;

// Usamos User en lugar de Professional
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function index(): \Inertia\Response
    {
        $customer = Auth::guard('customer');
        if (!$customer->check()) {
            abort(403);
        }

        $bookings = Booking::getBookingsByCustomer($customer->id());

        return Inertia::render('Booking/bookingList', [
            'bookings' => $bookings,
        ]);
    }

    // Mostrar la vista de la reserva y los servicios disponibles
    public function show()
    {
        // Obtener los usuarios que tienen el rol de 'profesional'
        $professionals = User::whereHas('roles', function ($query) {
            $query->where('slug', 'profesional'); // Filtrar por el rol 'profesional'
        })->get();


        // Obtener los servicios activos
        $services = Service::where('status', 'active')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => number_format($service->price, 2),
                    'duration' => $service->duration,
                    'status' => $service->status,
                ];
            });
        return Inertia::render('Booking/booking', [
            'initialServices' => $services,
        ]);
    }

    // Guardar la reserva
    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            abort(403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_id' => 'required|exists:customers,id',
            'professional_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
        ]);

        Booking::create([
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'professional_id' => $request->professional_id,
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('bookings.index')->with([
            'flash' => [
                'status_code' => 201,
                'message' => __('Booking created successfully')
            ]
        ]);
    }
}
