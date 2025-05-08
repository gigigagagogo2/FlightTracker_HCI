<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Flight extends Model
{
    protected $fillable = [
        'airplane_model_id',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
    ];

    // ▶️ assicurati che departure_time/arrival_time siano Carbon
    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time'   => 'datetime',
    ];

    // ▶️ appendi l’attributo virtuale status
    protected $appends = ['status'];

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

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_flight');
    }

    /**
     * Calcola lo stato del volo:
     *  - yellow: now < departure_time
     *  - green: departure_time ≤ now ≤ arrival_time
     *  - red: now > arrival_time
     */
    public function getStatusAttribute()
    {
        $now = Carbon::now();

        if ($now->lt($this->departure_time)) {
            return 'yellow';
        }

        if ($now->between($this->departure_time, $this->arrival_time)) {
            return 'green';
        }

        return 'red';
    }
}
