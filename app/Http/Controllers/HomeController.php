<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Voli popolari scelti a caso
        $popolari = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])
            ->where('departure_time', '<=', $now)
            ->where('arrival_time', '>=', $now)
            ->inRandomOrder()
            ->limit(9)
            ->get();

        // Voli vicino a te (id aeroporto 1 o 3)
        $vicino = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])
            ->where('departure_time', '<=', $now)
            ->where('arrival_time', '>=', $now)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home', [
            'popolari' => $popolari,
            'vicino' => $vicino,
        ]);
    }
}
