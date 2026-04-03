<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function search(Request $request)
    {
        $query  = $request->input('query', '');
        $filter = $request->input('filter', '');
        $now    = Carbon::now();

        $flights = Flight::with(['airplaneModel', 'departureAirport', 'arrivalAirport'])
            ->where(function ($q) use ($query) {
                if ($query === '') return;
                $q->whereHas('departureAirport', function ($sub) use ($query) {
                    $sub->whereRaw("LOWER(REGEXP_REPLACE(name, '^aeroporto\\s*', '')) LIKE ?", ["%" . strtolower($query) . "%"])
                        ->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
                })->orWhereHas('arrivalAirport', function ($sub) use ($query) {
                    $sub->whereRaw("LOWER(REGEXP_REPLACE(name, '^aeroporto\\s*', '')) LIKE ?", ["%" . strtolower($query) . "%"])
                        ->orWhereRaw('LOWER(city) LIKE ?', ["%" . strtolower($query) . "%"]);
                });
            });

        switch ($filter) {
            case 'in_arrivo':
                // Voli in volo che arrivano nelle prossime 2 ore
                $flights->where('departure_time', '<=', $now)
                    ->where('arrival_time', '>=', $now)
                    ->where('arrival_time', '<=', $now->copy()->addHours(2))
                    ->orderBy('arrival_time', 'asc');
                break;

            case 'in_partenza':
                // Voli che partono nelle prossime 2 ore
                $flights->where('departure_time', '>=', $now)
                    ->where('departure_time', '<=', $now->copy()->addHours(2))
                    ->orderBy('departure_time', 'asc');
                break;

            case 'atterrati':
                // Voli già atterrati (arrival_time nel passato)
                $flights->where('arrival_time', '<', $now)
                    ->orderBy('arrival_time', 'desc');  // prima i più recenti
                break;
                
            case str_starts_with($filter, 'paese_'):
                $paese = substr($filter, 6);
                $flights->where(function($q) use ($paese) {
                    $q->whereHas('departureAirport', fn($s) => $s->where('country', $paese))
                        ->orWhereHas('arrivalAirport',  fn($s) => $s->where('country', $paese));
                })->where('departure_time', '<=', $now)
                    ->where('arrival_time',   '>=', $now)
                    ->orderBy('departure_time', 'asc');
                break;

            default:

                $flights->orderByRaw("
                CASE
                    WHEN departure_time <= ? AND arrival_time >= ? THEN 1
                    WHEN departure_time > ? THEN 2
                    ELSE 3
                END
            ", [$now, $now, $now])
                    ->orderBy('departure_time', 'asc');
                break;
        }

        $results = $flights->limit(20)->get();

        $results->each(function ($flight) use ($now) {
            $dep = Carbon::parse($flight->departure_time);
            $arr = Carbon::parse($flight->arrival_time);

            if ($dep <= $now && $arr >= $now) {
                $flight->status = 'green';   // in volo
            } elseif ($dep > $now && $dep <= $now->copy()->addHours(2)) {
                $flight->status = 'yellow';  // in partenza
            } elseif ($arr < $now) {
                $flight->status = 'red';     // atterrato
            } else {
                $flight->status = 'gray';
            }
        });

        return response()->json($results);
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

        $a = sin($dLat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) ** 2;

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

            $distance = $this->haversineDistance($flight->departureAirport->latitude, $flight->departureAirport->longitude, $flight->arrivalAirport->latitude, $flight->arrivalAirport->longitude);

            $averageSpeed = $durationHours > 0 ? $distance / $durationHours : 0;

            return view('flights.archived', compact('flight', 'distance', 'durationMinutes', 'averageSpeed'));
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
