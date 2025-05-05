<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [
        'name',
        'city',
        'country',
        'latitude',
        'longitude',
    ];
    public function departingFlights()
    {
        return $this->hasMany(Flight::class, 'departure_airport_id');
    }

    public function arrivingFlights()
    {
        return $this->hasMany(Flight::class, 'arrival_airport_id');
    }

}
