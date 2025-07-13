<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Services;
use App\Http\Controllers\MercadoPagoController;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\Welcome::class, 'index'])->name('welcome');
Route::get('/service/{slug}', [\App\Http\Controllers\Services::class, 'show'])->name('service');

// Rutas de autenticación para clientes
Route::middleware(['guest:customer'])->prefix('customer')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Customer/Login', []);
    })->name('customer.login');
    
    Route::get('/register', function () {
        return Inertia::render('Customer/Register', []);
    })->name('customer.register');
    
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('customer.register.post');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('customer.login.post');
});

// Rutas protegidas para clientes
Route::middleware([\App\Http\Middleware\RedirectIfNotCustomer::class, 'auth:customer'])->prefix('customer')->group(function () {
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('customer.logout');
    
    Route::get('/settings', function () {
        return Inertia::render('Customer/Settings', []);
    })->name('customer.settings');
});

// Rutas de reservas con autenticación
Route::middleware(['auth:customer'])->group(function () {
    // Páginas principales
    Route::get('/booking/{service?}', [BookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::get('/bookingList', [BookingController::class, 'list'])->name('booking.BookingList');
    
    // APIs para crear y confirmar reservas
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/{booking}/confirm', [BookingController::class, 'confirmBooking'])->name('booking.confirm');
    Route::post('/booking/confirm-store', [BookingController::class, 'confirmStore'])->name('booking.confirm-store');
    
    // APIs para obtener datos dinámicos (AJAX)
    Route::get('/api/professionals/{service}', [BookingController::class, 'getProfessionalsByService'])->name('api.professionals.by-service');
    Route::get('/api/available-dates', [BookingController::class, 'getAvailableDates'])->name('api.available-dates');
    Route::get('/api/available-slots', [BookingController::class, 'getAvailableSlots'])->name('api.available-slots');
    
    // Eliminación y actualización de reservas
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
});

// Rutas de reservas sin autenticación
Route::get('/booking/success', function () {
    return Inertia::render('BookingSuccess', []);
})->name('booking.success');

// API Routes
Route::prefix('api')->group(function () {
    Route::post('/mercadopago/create-preference', [MercadoPagoController::class, 'createPreference']);
    Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook'])->name('payment.webhook');
});

// Rutas de retorno de MercadoPago
Route::get('/payment/success', [MercadoPagoController::class, 'success'])->name('payment.success');
Route::get('/payment/failure', [MercadoPagoController::class, 'failure'])->name('payment.failure');
Route::get('/payment/pending', [MercadoPagoController::class, 'pending'])->name('payment.pending');
Route::post('/payment/webhook', [MercadoPagoController::class, 'webhook'])->name('payment.webhook');

// Rutas de páginas informativas
Route::prefix('tools')->group(function () {
    Route::get('/blog', function () {
        return Inertia::render('Tools/Blog', []);
    })->name('tools.blog');
     Route::get('/about', [AboutController::class, 'index'])->name('tools.about');
    Route::get('/pqrs', function () {
        return Inertia::render('Tools/PQRS', []);
    })->name('tools.pqrs');
    Route::get('/terms', function () {
        return Inertia::render('Tools/Terms', []);
    })->name('tools.terms');
    Route::get('/careers', function () {
        return Inertia::render('Tools/Careers', []);
    })->name('tools.careers');
});

// API Routes para booking (redundantes, se mantienen por compatibilidad)
Route::prefix('api/booking')->group(function () {
    Route::get('professionals/{serviceId}', [BookingController::class, 'getProfessionalsByService']);
    Route::post('available-slots', [BookingController::class, 'getAvailableSlots']);
    Route::post('available-dates', [BookingController::class, 'getAvailableDates']);
    Route::post('create', [BookingController::class, 'store']);
    Route::post('confirm-store', [BookingController::class, 'confirmStore']);
});
