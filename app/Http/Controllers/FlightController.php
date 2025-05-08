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

    public function show($id)
    {
        $flight = Flight::with(['airplaneModel', 'departureAirport', 'arrivalAirport'])->findOrFail($id);
        return view('/flights/show_card', compact('flight'));
    }

    public function aggiungiPreferito(Request $request)
    {
        $user = auth()->user();
        $user->flights()->syncWithoutDetaching([$request->flight_id]);

        return response()->json(['success' => true]);
    }

    public function rimuoviPreferito(Request $request)
    {
        $user = auth()->user();
        $user->flights()->detach($request->flight_id);

        return response()->json(['success' => true]);
    }


}
