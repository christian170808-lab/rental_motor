<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    protected $fillable = ['customer_name','vehicle_name','plate_number','reason','cancelled_date'];
}
