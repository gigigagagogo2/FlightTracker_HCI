<?php

namespace Database\Seeders;

use App\Models\AirplaneModel;
use Illuminate\Database\Seeder;
use App\Models\Plane;

class PlaneSeeder extends Seeder
{
    public function run()
    {
        $planeNames = [
            'Boeing 737 800',
            'Airbus A320',
            'Boeing 777 300ER',
            'Airbus A350 900',
            'Boeing 787 9 Dreamliner',
            'Airbus A330 300',
            'Boeing 747 400',
            'Airbus A380',
            'Embraer E195 E2',
            'Bombardier CRJ900',
        ];

        foreach ($planeNames as $name) {
            $fileName = str_replace(' ', '_', $name) . '.png';

            AirplaneModel::create([
                'name' => $name,
                'image_path' => 'images/planes/' . $fileName,
            ]);
        }
    }
}
