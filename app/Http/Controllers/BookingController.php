<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use App\Models\User;
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

    public function show()
    {
        $professionals = User::whereHas('roles', function ($query) {
            $query->where('slug', 'profesional');
        })->get();

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
        ]);
    }

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
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'payment_transaction_id' => 'required|string',
            'payment_amount' => 'required|numeric',
            'card_type' => 'nullable|string',
            'bank_name' => 'nullable|string',
        ]);

        $booking = Booking::create($request->all());

        return redirect()->route('bookings.index')->with([
            'flash' => [
                'status_code' => 201,
                'message' => __('Booking created successfully'),
            ],
        ]);
    }
}