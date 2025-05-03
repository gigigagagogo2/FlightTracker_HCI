<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'departure_airport',
        'arrival_airport',
        'departure_time',
        'arrival_time',
    ];
}
