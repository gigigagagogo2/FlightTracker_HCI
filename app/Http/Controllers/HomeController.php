<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $popolari = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])
            ->where('departure_time', '<=', $now)
            ->where('arrival_time', '>=', $now)
            ->inRandomOrder()
            ->limit(9)
            ->get();

        return view('home', [
            'popolari' => $popolari,
        ]);
    }

    public function vicino(Request $request)
    {
        $paese = $request->input('paese');
        $now = Carbon::now();

        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])
            ->where('departure_time', '<=', $now)
            ->where('arrival_time', '>=', $now)
            ->where(function($q) use ($paese) {
                $q->whereHas('departureAirport', fn($s) => $s->where('country', $paese))
                    ->orWhereHas('arrivalAirport', fn($s) => $s->where('country', $paese));
            })
            ->limit(9)
            ->get();

        return response()->json($flights);
    }
}
