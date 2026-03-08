<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login',   'showLogin')->name('login');
    Route::post('/login',  'login')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| PROTECTED
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    // Vehicles
    Route::controller(VehicleController::class)->group(function () {
        Route::get('/vehicles',         'index')->name('vehicles.index');
        Route::post('/vehicles',        'store')->name('vehicles.store');
        Route::put('/vehicles/{id}',    'update')->name('vehicles.update');
        Route::delete('/vehicles/{id}', 'destroy')->name('vehicles.destroy');
    });

    // Vehicle Types
    Route::prefix('vehicle-types')->name('vehicle-types.')->group(function () {
        Route::get('/list',    [VehicleController::class, 'typeList'])   ->name('list');
        Route::get('/',        [VehicleController::class, 'typeIndex'])  ->name('index');
        Route::post('/',       [VehicleController::class, 'typeStore'])  ->name('store');
        Route::delete('/{id}', [VehicleController::class, 'typeDestroy'])->name('destroy');
    });

    // Booking
    Route::get('/booking/vehicles-json', [BookingController::class, 'vehiclesJson'])->name('booking.vehicles.json');

    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::get('/',                   'index')->name('booking.index');
        Route::get('/create/{id}',        'create')->name('bookings.create');
        Route::post('/store',             'store')->name('bookings.store');
        Route::get('/vehicles/{id}/edit', 'edit')->name('booking.edit');
        Route::put('/vehicles/{id}',      'update')->name('booking.update');
        Route::delete('/vehicles/{id}',   'destroy')->name('booking.destroy');
        Route::post('/return/{id}',       'returnVehicle')->name('booking.return');
        Route::get('/customer/{id}',      'getCustomer')->name('booking.customer');
    });

    // Returns
    Route::prefix('returns')->controller(ReturnController::class)->group(function () {
        Route::get('/',                    'index')->name('returns.index');
        Route::get('/create/{vehicle_id}', 'create')->name('returns.create');
        Route::post('/store',              'store')->name('returns.store');
    });

    // Customers
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/customers',         'index')->name('customers.index');
        Route::post('/customers',        'store')->name('customers.store');
        Route::put('/customers/{id}',    'update')->name('customers.update');
        Route::delete('/customers/{id}', 'destroy')->name('customers.destroy');
    });

    // Payments
    Route::controller(PaymentController::class)->group(function () {
        Route::get('/payments',      'index')->name('payments.index');
        Route::get('/payments/{id}', 'show')->name('payments.show');
    });

    // Reports
    Route::prefix('reports')->controller(ReportController::class)->group(function () {
        Route::get('/',         'index')->name('reports.index');
        Route::get('/pdf',      'downloadPdf')->name('reports.pdf');
        Route::get('/{id}/pdf', 'downloadSinglePdf')->name('reports.pdf.single');
    });

    // Admin
    Route::controller(AdminController::class)->group(function () {
        Route::get('/admin',         'index')->name('admin.index');
        Route::post('/admin',        'store')->name('admin.store');
        Route::put('/admin/{id}',    'update')->name('admin.update');
        Route::delete('/admin/{id}', 'destroy')->name('admin.destroy');
    });

});