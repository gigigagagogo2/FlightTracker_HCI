<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $flights = Flight::with(['airplaneModel', 'departureAirport', 'arrivalAirport'])->whereHas('departureAirport', function ($q) use ($query) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($query) . "%"])->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
        })->orWhereHas('arrivalAirport', function ($q) use ($query) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%" . strtolower($query) . "%"])->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
        })->take(10)->get();

        return response()->json($flights);
    }

    public function show($id)
    {
        $flight = Flight::with(['airplaneModel', 'departureAirport', 'arrivalAirport'])->findOrFail($id);

        if ($flight->status === 'red') {
            return view('flights.archived', compact('flight'));
        }

        return view('flights.card', compact('flight'));
    }


    public function aggiungiPreferito($id)
    {
        $user = auth()->user();
        $user->flights()->syncWithoutDetaching([$id]);

        return response()->json(['success' => true]);
    }

    public function rimuoviPreferito($id)
    {
        $user = auth()->user();
        $user->flights()->detach($id);

        return response()->json(['success' => true]);
    }


}
