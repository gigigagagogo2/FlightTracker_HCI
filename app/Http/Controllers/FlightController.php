<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flight;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $flights = Flight::with(['airplaneModel','departureAirport', 'arrivalAirport'])
            ->whereHas('departureAirport', function ($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($query) . "%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
            })
            ->orWhereHas('arrivalAirport', function ($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($query) . "%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
            })
            ->take(10)
            ->get();


        return response()->json($flights);
    }
}
