<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'airplane_model_id',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
    ];

    public function airplaneModel()
    {
        return $this->belongsTo(AirplaneModel::class, 'airplane_model_id');
    }

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }



}
