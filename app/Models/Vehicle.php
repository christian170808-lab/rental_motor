<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    // Kolom yang boleh diisi via mass assignment
    protected $fillable = [
        'name',
        'type',          // scooter, sport, adventure
        'image',         // Nama file foto motor di public/image/
        'plate_number',
        'price_per_day',
        'status',        // available, rented
    ];

    // Cast tipe data kolom tertentu
    protected $casts = [
        'price_per_day' => 'integer',
    ];

    /**
     * Relasi: Satu kendaraan bisa memiliki banyak booking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}