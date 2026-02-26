<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

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
        'payment_type',   // ← TAMBAH INI
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function returnVehicle()
    {
        return $this->hasOne(ReturnVehicle::class, 'booking_id');
    }
}