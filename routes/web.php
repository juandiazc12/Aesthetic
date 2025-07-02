<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Services;
use App\Http\Controllers\PagesController; // Nuevo controlador
use Inertia\Inertia;
use App\Http\Controllers\BinLookController;

Route::get('/', [\App\Http\Controllers\Welcome::class, 'index'])->name('welcome');
Route::get('/service/{slug}', [\App\Http\Controllers\Services::class, 'show'])->name('service');

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

Route::middleware([\App\Http\Middleware\RedirectIfNotCustomer::class])->prefix('customer')->group(function () {
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('customer.logout');
    Route::get('/binlist/{bin}', [BinLookController::class, 'getBankInfo']);
    // Nueva ruta para configuración del cliente
    Route::get('/settings', function () {
        return Inertia::render('Customer/Settings', []);
    })->name('customer.settings');
});

Route::get('/booking/{service}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

Route::get('/booking/success', function () {
    return Inertia::render('BookingSuccess', []);
});

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

Route::get('/api/binlist/{bin}', [BinLookController::class, 'getBankInfo']);

Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);

// Nuevas rutas para las páginas del footer
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