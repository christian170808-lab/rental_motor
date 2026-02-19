<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\CustomerController;

Route::redirect('/', '/login');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('/succes', 'auth.succes')->name('succes');

    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicles', 'index')->name('vehicles.index');
    });

    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::get('/', 'index')->name('booking.index');
        Route::get('/create/{id}', 'create')->name('bookings.create');
        Route::post('/store', 'store')->name('bookings.store');
        Route::get('/pdf/{id}', 'downloadPdf')->name('booking.pdf');
        Route::put('/verify/{id}', 'verifyPayment')->name('bookings.verify');
    });

    Route::prefix('returns')->controller(ReturnController::class)->group(function () {
        Route::get('/', 'index')->name('returns.index');
        Route::get('/create/{vehicle_id}', 'create')->name('returns.create');
        Route::post('/store', 'store')->name('returns.store');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index')->name('customers.index');
    });
});