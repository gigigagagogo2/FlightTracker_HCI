<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'nickname' => 'admin',
            'email' => 'admin@site.it',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);


        User::create([
            'nickname' => 'gigi',
            'email' => 'robertbararu2@gmail.com',
            'password' => Hash::make('gigi'),
            'is_admin' => false,
        ]);

        User::create([
            'nickname' => 'test',
            'email' => 'test@test.it',
            'password' => Hash::make('test'),
            'is_admin' => false,
        ]);

        User::create([
            'nickname' => 'prova',
            'email' => 'prova@prova.it',
            'password' => Hash::make('prova'),
            'is_admin' => false,
        ]);
    }
}
