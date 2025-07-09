<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Services\FlightSimulationService;
use Illuminate\Http\Request;

class FlightSimulationController extends Controller
{
    public function getMultipleFlightData(Request $request)
    {
        $ids = $request->input('ids');

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->whereIn('id', $ids)
            ->get();

        $service = new FlightSimulationService();
        return response()->json($service->simulateMultipleFlights($flights));
    }



}
