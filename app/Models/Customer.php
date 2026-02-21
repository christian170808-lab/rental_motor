<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi via mass assignment
    protected $fillable = [
        'customer_name',
        'customer_id',  // Unique customer code (e.g. CUST001)
        'email',
        'phone',        // Phone number column in database
    ];

    /**
     * Relasi: Satu customer bisa memiliki banyak booking
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
