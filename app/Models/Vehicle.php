<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    // Menggunakan factory untuk pembuatan data dummy
    use HasFactory;

    // Mengizinkan mass assignment untuk field tertentu
    protected $fillable = [
        'name',
        'image',
        'plate_number',
        'price_per_day',
        'status'
    ];

    // Relasi: Satu kendaraan dapat memiliki banyak data booking
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}