<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Services\FlightSimulationService;
use Illuminate\Support\Facades\Auth;

class FlightSimulationController extends Controller
{
    public function getFlightData(int $id)
    {
        $simulationService = new FlightSimulationService();
        $data = $simulationService->simulateFlight($id);

        return response()->json($data);
    }



}
