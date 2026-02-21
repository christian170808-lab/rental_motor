<?php
// ============================================================
// MODEL: Booking
// File: app/Models/Booking.php
// Merepresentasikan data transaksi penyewaan motor
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    // Kolom yang boleh diisi via mass assignment (Booking::create / $booking->update)
    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'identity_number',
        'identity_card',
        'payment_proof',
        'start_date',
        'end_date',
        'total_cost',
        'payment_status',
    ];

    /**
     * Relasi: Satu booking bisa punya satu data pengembalian
     */
    public function returnVehicle()
    {
        return $this->hasOne(ReturnVehicle::class, 'booking_id');
    }

    /**
     * Relasi: Setiap booking dimiliki oleh satu customer
     * FK: customer_id di tabel bookings → id di tabel customers
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Relasi: Setiap booking terkait dengan satu kendaraan
     * FK: vehicle_id di tabel bookings → id di tabel vehicles
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}