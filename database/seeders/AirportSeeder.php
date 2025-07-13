<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Airport;

class AirportSeeder extends Seeder
{
    public function run()
    {
        $airports = [
            [
                'name' => 'Aeroporto di Milano Malpensa',
                'city' => 'Milano',
                'country' => 'Italy',
                'latitude' => 45.630604,
                'longitude' => 8.728111,
            ],
            [
                'name' => 'Aeroporto di Roma Fiumicino',
                'city' => 'Roma',
                'country' => 'Italy',
                'latitude' => 41.800278,
                'longitude' => 12.238889,
            ],
            [
                'name' => 'Aeroporto di Venezia Marco Polo',
                'city' => 'Venezia',
                'country' => 'Italy',
                'latitude' => 45.505278,
                'longitude' => 12.351944,
            ],
            [
                'name' => 'Aeroporto Internazionale di Tirana',
                'city' => 'Tirana',
                'country' => 'Albania',
                'latitude' => 41.414700,
                'longitude' => 19.720600,
            ],
            [
                'name' => 'Aeroporto Charles de Gaulle',
                'city' => 'Parigi',
                'country' => 'Francia',
                'latitude' => 49.009690,
                'longitude' => 2.547925,
            ],
            [
                'name' => 'Aeroporto di Francoforte',
                'city' => 'Francoforte',
                'country' => 'Germania',
                'latitude' => 50.033333,
                'longitude' => 8.570556,
            ],
            [
                'name' => 'Aeroporto di Zurigo',
                'city' => 'Zurigo',
                'country' => 'Svizzera',
                'latitude' => 47.464722,
                'longitude' => 8.549167,
            ],
            [
                'name' => 'Aeroporto di Vienna',
                'city' => 'Vienna',
                'country' => 'Austria',
                'latitude' => 48.110278,
                'longitude' => 16.569722,
            ],
            [
                'name' => 'Aeroporto di Amsterdam Schiphol',
                'city' => 'Amsterdam',
                'country' => 'Paesi Bassi',
                'latitude' => 52.308613,
                'longitude' => 4.763889,
            ],
            [
                'name' => 'Aeroporto di Madrid-Barajas',
                'city' => 'Madrid',
                'country' => 'Spagna',
                'latitude' => 40.472222,
                'longitude' => -3.560833,
            ],
        ];


        foreach ($airports as $airport) {
            Airport::create($airport);
        }
    }
}
