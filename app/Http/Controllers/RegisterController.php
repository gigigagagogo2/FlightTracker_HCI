<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {

        // valida i dati ricevuti dal form
        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',

        ]);

        // Crea lo user
        $user = User::create([
            'nickname' => $validated['nickname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Logga in automatico
        auth()->login($user);

        // Redirect con messaggio
        return redirect('/')->with('success', 'Registrazione completata con successo');
    }
}
