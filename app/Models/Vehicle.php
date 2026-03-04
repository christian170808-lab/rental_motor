<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | TABLE CONFIGURATION
    |--------------------------------------------------------------------------
    */
    protected $table = 'vehicles';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNABLE FIELDS
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'type',          // scooter | sport | trail
        'image',         // filename pada folder public/image
        'plate_number',
        'price_per_day',
        'status',        // available | rented
    ];

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE CASTING
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'price_per_day' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Satu kendaraan dapat memiliki banyak booking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vehicle_id', 'id');
    }

    /**
     * Type kendaraan dari tabel vehicle_types
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'type', 'name');
    }
}