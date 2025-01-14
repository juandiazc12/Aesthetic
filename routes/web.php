<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Services;
use Inertia\Inertia;

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
});

Route::get('/booking/{service}', [BookingController::class, 'show'])->name('booking.show');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

Route::get('/booking/success', function () {
    return Inertia::render('BookingSuccess', []);
});