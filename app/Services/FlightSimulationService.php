<?php

namespace App\Services;

use App\Models\Flight;
use Carbon\Carbon;

class FlightSimulationService
{
    private function simulateSingleFlight(Flight $flight): ?array
    {
        $startTime = Carbon::parse($flight->departure_time);
        $endTime = Carbon::parse($flight->arrival_time);

        $totalTime = $startTime->diffInSeconds($endTime);
        $progress = $this->getProgress($startTime, $totalTime);

        $startCoords = ['lat' => $flight->departureAirport->latitude, 'lng' => $flight->departureAirport->longitude];
        $endCoords = ['lat' => $flight->arrivalAirport->latitude, 'lng' => $flight->arrivalAirport->longitude];

        if ($progress == 1) {
            return ['id' => $flight->id, 'progress' => $progress,];
        } elseif ($progress == 0) {
            $currentPosition = $startCoords;
            $speed = 0;
        } else {
            $currentPosition = $this->getCurrentPoint($startCoords, $endCoords, $progress);
            $distance = $this->haversineGreatCircleDistance($startCoords, $endCoords);
            $averageSpeed = $distance / $totalTime * 3.6;
            $elapsedTime = $startTime->diffInSeconds(Carbon::now(), true);
            $speed = $this->getActualSpeed($averageSpeed, $elapsedTime, $totalTime);
        }

        return ['id' => $flight->id, 'lat' => (float)$currentPosition['lat'], 'lng' => (float)$currentPosition['lng'], 'speed' => $speed, 'progress' => $progress, 'arrival_city' => $flight->arrivalAirport->city, 'departure_city' => $flight->departureAirport->city,];
    }

    // Questo metodo ritorna solo i voli non gia atterrati
    public function simulateMultipleFlights($flights): array
    {
        $results = [];

        foreach ($flights as $flight) {
            $results[$flight->id] = $this->simulateSingleFlight($flight);

        }

        return $results;
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param $startCoords
     * @param $endCoords
     * @param int $earthRadius Mean earth radius in [m]
     * @return float|int Distance between points in [m] (same as earthRadius)
     */
    function haversineGreatCircleDistance($startCoords, $endCoords, int $earthRadius = 6371000): float|int
    {
        // convert from degrees to radians
        $latFrom = deg2rad($startCoords['lat']);
        $lngFrom = deg2rad($startCoords['lng']);
        $latTo = deg2rad($endCoords['lat']);
        $lngTo = deg2rad($endCoords['lng']);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    function getCurrentPoint($startCoords, $endCoords, $progress): array
    {
        // convert from degrees to radians
        $latFrom = deg2rad($startCoords['lat']);
        $lngFrom = deg2rad($startCoords['lng']);
        $latTo = deg2rad($endCoords['lat']);
        $lngTo = deg2rad($endCoords['lng']);

        // Calcola le differenze
        $dlat = $latTo - $latFrom;
        $dlng = $lngTo - $lngFrom;

        // Haversine formula
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($latFrom) * cos($latTo) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Calcoliamo la posizione intermedia
        $a = sin((1 - $progress) * $c) / sin($c); // proporzione della distanza percorsa
        $b = sin($progress * $c) / sin($c); // proporzione restante della distanza

        // Calcoliamo la latitudine e longitudine del punto intermedio
        $x = $a * cos($latFrom) * cos($lngFrom) + $b * cos($latTo) * cos($lngTo);
        $y = $a * cos($latFrom) * sin($lngFrom) + $b * cos($latTo) * sin($lngTo);
        $z = $a * sin($latFrom) + $b * sin($latTo);

        $latMid = atan2($z, sqrt($x * $x + $y * $y));
        $lngMid = atan2($y, $x);

        // Convertiamo la latitudine e longitudine in gradi
        $latMid = rad2deg($latMid);
        $lngMid = rad2deg($lngMid);

        // Ritorniamo la latitudine e longitudine intermedia
        return ['lat' => round($latMid, 6), 'lng' => round($lngMid, 6)];
    }

    public function getProgress(Carbon $start, float $totalTime): float
    {
        $now = Carbon::now();

        $elapsedTime = $start->diffInSeconds($now);
        $progress = $elapsedTime / $totalTime;

        return max(0, min(1, $progress));
    }

    public function getActualSpeed(float $averageSpeed, float $elapsedTime, $totalTime, float $tempoAccelerazione = 120): float
    {
        $maxSpeed = $averageSpeed + 30;

        if ($elapsedTime < $tempoAccelerazione) {
            $speed = $maxSpeed * (1 - exp(-$elapsedTime / ($tempoAccelerazione / 12)));
        } elseif ($elapsedTime < ($totalTime - $tempoAccelerazione)) {
            $speed = $maxSpeed + rand(-1, 1);
        } else {
            $speed = $maxSpeed * (1 - exp(-($totalTime - $elapsedTime) / (($totalTime - $tempoAccelerazione) / 12)));
        }

        return round($speed);
    }

}
