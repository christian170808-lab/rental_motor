<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_id',
        'email',
        'phone_number',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}