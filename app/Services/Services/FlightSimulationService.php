<?php

namespace App\Services\Services;
use App\Models\Flight;
use Carbon\Carbon;

class FlightSimulationService{
    public function simulateFlight(int $flightId): array{

        $flight = Flight::with(['departureAirport', 'arrivalAirport'])->findOrFail($flightId);

        $startTime = Carbon::parse($flight->departure_time);
        $endTime = Carbon::parse($flight->arrival_time);
        $progress = $this->getProgress($startTime, $endTime);

        $startCoords = ['lat' => $flight->departureAirport->latitude, 'lon' => $flight->departureAirport->longitude];
        $endCoords = ['lat' => $flight->arrivalAirport->latitude, 'lon' => $flight->arrivalAirport->longitude];
        $currentPosition = $this->getCoordinates($startCoords, $endCoords, $progress);
        $speed = $this->getActualSpeed($progress);

        return [
            'lat' => $currentPosition['lat'],
            'lon' => $currentPosition['lon'],
            'velocita' => $speed,
            'stato' => $progress != 1 ? 'In volo' : 'Atterrato',
            'percentuale' => $progress * 100
        ];
    }

    public function getProgress(Carbon $start, Carbon $end): float{
        $now = Carbon::now();

        $totalTime = $end->diffInSeconds($start);
        $elapsedTime = $now->diffInSeconds($start, false);
        $progress = $elapsedTime / $totalTime;

        return max(0, min(1, $progress));
    }

    public function getCoordinates(array $start, array $end, float $progress):array{
        $lat = $start['lat'] + ($end['lat'] - $start['lat']) * $progress;
        $lon = $start['lon'] + ($end['lon'] - $start['lon']) * $progress;

        return [
            'lat' => round($lat, 6),
            'lon' => round($lon, 6),
        ];
    }

    public function getActualSpeed(float $progress): float{
        $maxSpeed = 850;

        if ($progress < 0.1) {
            $speed = $maxSpeed * pow($progress / 0.1, 2);
        } elseif ($progress < 0.9) {
            $speed = $maxSpeed + rand(-20, 20);
        } else {
            $speed = $maxSpeed * pow((1 - $progress) / 0.1, 2);
        }

        return round($speed, 2);
    }

}
