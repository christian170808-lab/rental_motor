<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'vehicle_id',
        'customer_name',
        'identity_number', // Nomor KTP (NIK) 16 digit
        'identity_card',   // Nama file foto KTP
        'start_date',
        'end_date',
        'total_cost',
        'payment_status',
    ];

    // --- Nama fungsi adalah 'vehicle' ---
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}