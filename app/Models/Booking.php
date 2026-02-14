<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    // Mengizinkan mass assignment untuk field tertentu
    protected $fillable = [
        'vehicle_id',
        'customer_name',
        'identity_card',
        'start_date',
        'end_date',
        'total_cost',
        'payment_status',
    ];

    // Relasi: Booking ini dimiliki oleh satu data Vehicle
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}