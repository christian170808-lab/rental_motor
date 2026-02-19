<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnVehicle extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'booking_id',
        'return_date',
        'late_days',
        'penalty',
        'vehicle_condition',
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'penalty'     => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}