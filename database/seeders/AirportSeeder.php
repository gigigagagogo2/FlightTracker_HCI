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
                'country' => 'Italia',
                'latitude' => 45.630604,
                'longitude' => 8.728111,
                'image_path' => 'images/city/milano_malpensa.jpg',
            ],
            [
                'name' => 'Aeroporto di Roma Fiumicino',
                'city' => 'Roma',
                'country' => 'Italia',
                'latitude' => 41.800278,
                'longitude' => 12.238889,
                'image_path' => 'images/city/roma_fiumicino.jpg',
            ],
            [
                'name' => 'Aeroporto di Venezia Marco Polo',
                'city' => 'Venezia',
                'country' => 'Italia',
                'latitude' => 45.505278,
                'longitude' => 12.351944,
                'image_path' => 'images/city/venezia_marco_polo.jpg',
            ],
            [
                'name' => 'Aeroporto Internazionale di Tirana',
                'city' => 'Tirana',
                'country' => 'Albania',
                'latitude' => 41.414700,
                'longitude' => 19.720600,
                'image_path' => 'images/city/albania_tirana.jpg',
            ],
            [
                'name' => 'Aeroporto Charles de Gaulle',
                'city' => 'Parigi',
                'country' => 'Francia',
                'latitude' => 49.009690,
                'longitude' => 2.547925,
                'image_path' => 'images/city/parigi_charles.jpg',
            ],
            [
                'name' => 'Aeroporto di Francoforte',
                'city' => 'Francoforte',
                'country' => 'Germania',
                'latitude' => 50.033333,
                'longitude' => 8.570556,
                'image_path' => 'images/city/germania_francoforte.jpg',
            ],
            [
                'name' => 'Aeroporto di Zurigo',
                'city' => 'Zurigo',
                'country' => 'Svizzera',
                'latitude' => 47.464722,
                'longitude' => 8.549167,
                'image_path' => 'images/city/svizzera_zurigo.jpg',
            ],
            [
                'name' => 'Aeroporto di Vienna',
                'city' => 'Vienna',
                'country' => 'Austria',
                'latitude' => 48.110278,
                'longitude' => 16.569722,
                'image_path' => 'images/city/austria_vienna.jpg',
            ],
            [
                'name' => 'Aeroporto di Amsterdam Schiphol',
                'city' => 'Amsterdam',
                'country' => 'Paesi Bassi',
                'latitude' => 52.308613,
                'longitude' => 4.763889,
                'image_path' => 'images/city/paesi_bassi_amsterdam.jpg',
            ],
            [
                'name' => 'Aeroporto di Madrid-Barajas',
                'city' => 'Madrid',
                'country' => 'Spagna',
                'latitude' => 40.472222,
                'longitude' => -3.560833,
                'image_path' => 'images/city/spagna_madrid.jpg',
            ],
        ];


        foreach ($airports as $airport) {
            Airport::create($airport);
        }
    }
}
