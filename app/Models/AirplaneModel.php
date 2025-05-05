<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirplaneModel extends Model
{
    protected $fillable = [
        'name',
        'image_path',
    ];

    public function flights()
    {
        return $this->hasMany(Flight::class, 'airplane_model_id');
    }
}

