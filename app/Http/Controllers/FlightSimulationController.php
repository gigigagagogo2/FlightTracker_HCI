<?php

namespace App\Http\Controllers;

use App\Services\Services\FlightSimulationService;

class FlightSimulationController extends Controller
{
    public function getFlightData(int $id)
    {
        $simulationService = new FlightSimulationService();
        $data = $simulationService->simulateFlight($id);

        return response()->json($data);
    }
}
