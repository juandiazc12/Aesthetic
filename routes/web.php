<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia\Inertia::render('Welcome', []);
});

Route::get('/customer/login', function () {
    return Inertia\Inertia::render('Customer/Login', []);
})->name('customer.login');
