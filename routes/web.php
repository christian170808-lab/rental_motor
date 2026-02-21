<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\CustomerController;

// Redirect root ke halaman login
Route::redirect('/', '/login');

// ============================================================
// AUTH ROUTES (tidak perlu login)
// ============================================================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');           // Tampilkan form login
    Route::post('/login', 'login')->name('login.process');      // Proses login
    Route::post('/logout', 'logout')->name('logout');           // Proses logout
});

// ============================================================
// PROTECTED ROUTES (harus login)
// ============================================================
Route::middleware('auth')->group(function () {

    // Dashboard utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---- VEHICLE ROUTES ----
    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicles', 'index')->name('vehicles.index');           // Daftar motor + return data
        Route::get('/vehicles/create', 'create')->name('vehicles.create'); // Form tambah motor
        Route::post('/vehicles', 'store')->name('vehicles.store');          // Simpan motor baru
    });

    // ---- BOOKING ROUTES ----
    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::get('/', 'index')->name('booking.index');                    // Daftar kendaraan untuk booking
        Route::get('/create/{id}', 'create')->name('bookings.create');     // Form booking kendaraan
        Route::post('/store', 'store')->name('bookings.store');            // Simpan booking baru
        Route::get('/pdf/{id}', 'downloadPdf')->name('booking.pdf');       // Download PDF laporan booking
        // FIX: Route verifyPayment dihapus karena method tidak ada di BookingController
    });

    // ---- RETURN ROUTES ----
    Route::prefix('returns')->controller(ReturnController::class)->group(function () {
        Route::get('/', 'index')->name('returns.index');                            // Riwayat pengembalian
        Route::get('/create/{vehicle_id}', 'create')->name('returns.create');      // Form pengembalian
        Route::post('/store', 'store')->name('returns.store');                      // Proses pengembalian
    });

    // ---- CUSTOMER ROUTES ----
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers', 'index')->name('customers.index');                 // Daftar customer
        Route::get('/customers/{id}/edit', 'edit')->name('customers.edit');         // Form edit customer
        Route::put('/customers/{id}', 'update')->name('customers.update');          // Update data customer
        Route::delete('/customers/{id}', 'destroy')->name('customers.destroy');     // Hapus customer
    });
});