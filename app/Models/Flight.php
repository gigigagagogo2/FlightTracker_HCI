<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Flight extends Model
{
    protected $fillable = [
        'airplane_model_id',
        'departure_airport_id',
        'arrival_airport_id',
        'departure_time',
        'arrival_time',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time'   => 'datetime',
    ];

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
    public function getStatusAttribute(): string
    {
        $now = Carbon::now();

        if ($now->between($this->departure_time->copy()->addHours(-2) ,$this->departure_time)) {
            return 'yellow';
        }

        if ($now->between($this->departure_time, $this->arrival_time)) {
            return 'green';
        }

        if ($now->greaterThan($this->arrival_time)) {
            return 'red';
        }

        return 'grey';

    }

    /**
     * Verifica se il volo è nei preferiti dell'utente autenticato.
     */

    public function isPreferito(): bool
    {
        // Verifica se l'utente autenticato è presente nella relazione molti-a-molti
        return $this->users->contains(auth()->user());
    }
}
