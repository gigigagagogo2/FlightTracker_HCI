<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Carbon\Carbon;
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

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function show($id)
    {
        $flight = Flight::with(['airplaneModel', 'departureAirport', 'arrivalAirport'])->findOrFail($id);

        if ($flight->status === 'red') {

            $departure = Carbon::parse($flight->departure_time);
            $arrival = Carbon::parse($flight->arrival_time);
            $durationMinutes = $departure->diffInMinutes($arrival);
            $durationHours = $durationMinutes / 60;

            $distance = $this->haversineDistance(
                $flight->departureAirport->latitude,
                $flight->departureAirport->longitude,
                $flight->arrivalAirport->latitude,
                $flight->arrivalAirport->longitude
            );

            $averageSpeed = $durationHours > 0 ? $distance / $durationHours : 0;

            return view('flights.archived', compact('flight', 'distance', 'durationMinutes', 'averageSpeed') );
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
