<?php

namespace App\Http\Controllers;

use App\Models\Flight;

class HomeController extends Controller
{
    public function index()
    {
        // Voli popolari scelti a caso
        $popolari = Flight::with(['departureAirport', 'arrivalAirport'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Voli vicino a te (id aeroporto 1 o 3)
        $vicino = Flight::with(['departureAirport', 'arrivalAirport'])
            ->whereHas('departureAirport', fn($q) => $q->whereIn('id', [1, 3]))
            ->orWhereHas('arrivalAirport', fn($q) => $q->whereIn('id', [1, 3]))
            ->take(4)
            ->get();

        return view('home', [
            'popolari' => $popolari,
            'vicino' => $vicino,
        ]);
    }
}
