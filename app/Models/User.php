<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'nickname',
        'email',
        'password',
        'is_admin',
        'profile_picture_path',
    ];

    public function flights()
    {
        return $this->belongsToMany(Flight::class, 'user_flight')->withPivot('notified');
    }

    public function notNotifiedFlightsRelation()
    {
        return $this->belongsToMany(Flight::class, 'user_flight')
            ->withPivot('notified')
            ->wherePivot('notified', false);
    }

}
