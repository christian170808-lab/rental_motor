<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnVehicle extends Model
{
    protected $table = 'returns'; // Nama tabel berbeda dari nama model

    // Kolom yang boleh diisi via mass assignment
    protected $fillable = [
        'booking_id',
        'return_date',
        'late_days',
        'penalty',
        'vehicle_condition',
    ];

    // Cast tipe data kolom tertentu
    protected $casts = [
        'return_date' => 'datetime', // Otomatis dikonversi ke Carbon object
        'penalty'     => 'integer',
    ];

    /**
     * Relasi: Setiap return terkait dengan satu booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
