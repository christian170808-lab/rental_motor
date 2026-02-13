<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\VehicleController;

// Halaman Publik
Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.process');
    Route::post('/logout', 'logout')->name('logout'); // Pastikan ini ada di controller
});

// Fitur yang Memerlukan Login
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');

    // Kendaraan
    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicles', 'index')->name('vehicles.index');
    });

    // Booking
    Route::controller(BookingController::class)->group(function () {
        Route::get('/booking', 'index')->name('booking.index');
        Route::get('/booking/create/{id}', 'create')->name('bookings.create');
        Route::post('/booking/store', 'store')->name('bookings.store');
        // --- PERBAIKAN: Tambah Route PDF ---
        Route::get('/booking/pdf/{id}', 'downloadPdf')->name('bookings.pdf');
    });

    Route::get('/booking/{id}/pdf', [BookingController::class, 'downloadPdf'])
    ->name('booking.pdf');


    // Pengembalian (Returns)
    Route::controller(ReturnController::class)->group(function () {
        Route::get('/returns', 'index')->name('returns.index');
        Route::get('/returns/create/{vehicle_id}', 'create')->name('returns.create');
        Route::post('/returns/store', 'store')->name('returns.store'); 
    });
});