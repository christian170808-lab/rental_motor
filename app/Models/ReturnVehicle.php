<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnVehicle extends Model
{
    // Menentukan nama tabel secara eksplisit jika berbeda dengan konvensi plural model
    protected $table = 'returns';

    // Mengizinkan mass assignment untuk field tertentu
    protected $fillable = [
        'booking_id',
        'return_date',
        'late_days',
        'penalty',
        'vehicle_condition'
    ];

    // Relasi: Pengembalian ini milik satu data Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}