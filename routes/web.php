<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Services;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\BinLookController;
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
Route::middleware([\App\Http\Middleware\RedirectIfNotCustomer::class])->prefix('customer')->group(function () {
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('customer.logout');
    
    Route::get('/settings', function () {
        return Inertia::render('Customer/Settings', []);
    })->name('customer.settings');
});

// Rutas de reservas
Route::get('/booking/{service}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success', function () {
    return Inertia::render('BookingSuccess', []);
})->name('booking.success');

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

// API Routes
Route::prefix('api')->group(function () {
    // BIN Lookup para información de tarjetas
    Route::get('/binlist/{bin}', [BinLookController::class, 'getBankInfo']);
    
    // MercadoPago API routes
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

    Route::get('/about', function () {
        return Inertia::render('Tools/About', []);
    })->name('tools.about');

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