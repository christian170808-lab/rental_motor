<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\VehicleController;

// Mengarahkan URL root ('/') langsung ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route grup untuk proses autentikasi (login/logout)
Route::controller(AuthController::class)->group(function () {
    // Menampilkan halaman form login
    Route::get('/login', 'showLogin')->name('login');
    // Memproses data login yang dikirim dari form
    Route::post('/login', 'login')->name('login.process');
    // Memproses logout pengguna
    Route::post('/logout', 'logout')->name('logout');
});

// Route grup yang dilindungi oleh middleware 'auth' (hanya bisa diakses jika sudah login)
Route::middleware(['auth'])->group(function () {
    
    // Menampilkan halaman dashboard setelah berhasil login
    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');

    // Route grup untuk manajemen data kendaraan
    Route::controller(VehicleController::class)->group(function () {
        // Menampilkan daftar kendaraan
        Route::get('/vehicles', 'index')->name('vehicles.index');
    });

    // Route grup untuk proses penyewaan (booking)
    Route::controller(BookingController::class)->group(function () {
        // Menampilkan daftar booking
        Route::get('/booking', 'index')->name('booking.index');
        // Menampilkan form untuk membuat booking baru berdasarkan ID kendaraan
        Route::get('/booking/create/{id}', 'create')->name('bookings.create');
        // Memproses penyimpanan data booking baru ke database
        Route::post('/booking/store', 'store')->name('bookings.store');
        // Mendownload laporan booking dalam bentuk PDF
        Route::get('/booking/pdf/{id}', 'downloadPdf')->name('bookings.pdf');
    });

    // Route grup untuk proses pengembalian kendaraan
    Route::controller(ReturnController::class)->group(function () {
        // Menampilkan daftar pengembalian kendaraan
        Route::get('/returns', 'index')->name('returns.index');
        // Menampilkan form pengembalian berdasarkan ID kendaraan yang disewa
        Route::get('/returns/create/{vehicle_id}', 'create')->name('returns.create');
        // Memproses penyimpanan data pengembalian dan menghitung denda jika ada
        Route::post('/returns/store', 'store')->name('returns.store'); 
    });
});