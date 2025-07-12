<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Airport;
use App\Models\AirplaneModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airports = Airport::all();
        $airplanes = AirplaneModel::all();

        if ($airports->count() < 2 || $airplanes->isEmpty()) {
            $this->command->warn('Not enough data to seed flights.');
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            // Prendi aeroporti diversi
            $departure = $airports->random();
            do {
                $arrival = $airports->random();
            } while ($arrival->id === $departure->id);

            if ($i < 5) {
                $departureTime = Carbon::now()->setSeconds(0);
            } elseif ($i < 8){
              $departureTime = Carbon::now()->copy()->addHours(rand(1, 3));
            }else {
                $departureTime = Carbon::now()->addDays(rand(-10, 30))
                    ->setTime(rand(0, 23), [0, 15, 30, 45][rand(0, 3)], 0);
            }

            #$arrivalTime = (clone $departureTime)->addMinutes(2);
            $arrivalTime = (clone $departureTime)->addHours(rand(1, 5))->addMinutes(rand(0, 59));

            Flight::create([
                'airplane_model_id'    => $airplanes->random()->id,
                'departure_airport_id' => $departure->id,
                'arrival_airport_id'   => $arrival->id,
                'departure_time'       => $departureTime,
                'arrival_time'         => $arrivalTime,
            ]);
        }

        $departure = $airports->random();
        do {
            $arrival = $airports->random();
        } while ($arrival->id === $departure->id);

        // Volo che decolla tra 15 secondi
        Flight::create([
            'airplane_model_id'    => $airplanes->random()->id,
            'departure_airport_id' => $departure->id,
            'arrival_airport_id'   => $arrival->id,
            'departure_time'       => Carbon::now()->addSeconds(15),
            'arrival_time'         => Carbon::now()->addHours(2),
        ]);

        // Volo che atterra tra 1 minuto
        Flight::create([
            'airplane_model_id'    => $airplanes->random()->id,
            'departure_airport_id' => $departure->id,
            'arrival_airport_id'   => $arrival->id,
            'departure_time'       => Carbon::now()->addHours(-1),
            'arrival_time'         => Carbon::now()->addMinutes(),
        ]);
    }
}
