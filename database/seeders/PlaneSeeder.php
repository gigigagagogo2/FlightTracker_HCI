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
            'Boeing_737_800',
            'Airbus_A320',
            'Boeing_777_300ER',
            'Airbus_A350_900',
            'Boeing_787_9_Dreamliner',
            'Airbus_A330_300',
            'Boeing_747_400',
            'Airbus_A380',
            'Embraer_E195_E2',
            'Bombardier_CRJ900',
        ];

        foreach ($planeNames as $name) {
            $fileName = $name . '.png';

            AirplaneModel::create([
                'name' => $name,
                'image_path' => 'images/planes/' . $fileName,
            ]);
        }
    }
}
