<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlightSimulationService;

class FlightSimulationController extends Controller
{
    public function getFlightData(int $id)
    {
        $simulationService = new FlightSimulationService();
        $data = $simulationService->simulateFlight($id);

        return response()->json($data);
    }
}
