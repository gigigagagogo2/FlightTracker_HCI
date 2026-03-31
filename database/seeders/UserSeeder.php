<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'nickname' => 'admin',
            'email' => 'admin@site.it',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        User::create([
            'nickname' => 'gigi',
            'email' => 'robertbararu2@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('gigi'),
            'is_admin' => false,
        ]);

        User::create([
            'nickname' => 'test',
            'email' => 'test@test.it',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('test'),
            'is_admin' => false,
        ]);

        User::create([
            'nickname' => 'prova',
            'email' => 'prova@prova.it',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('prova'),
            'is_admin' => false,
        ]);
    }
}
