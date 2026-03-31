<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255|unique:users',
            'email' => [
                'required',
                'email:rfc,strict',
                'unique:users',
                function ($attribute, $value, $fail) {
                    // Verifica che ci sia un TLD di almeno 2 caratteri
                    if (!preg_match('/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/', $value)) {
                        $fail('Inserisci un\'email valida con dominio completo (es. nome@gmail.com).');
                    }
            },
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],



        ], [
            'nickname.required'  => 'Il nickname è obbligatorio.',
            'nickname.unique'    => 'Questo nickname è già in uso.',
            'email.required'     => 'L\'email è obbligatoria.',
            'email.email'        => 'Inserisci un\'email valida.',
            'email.unique'       => 'Questa email è già registrata.',
            'password.required'  => 'La password è obbligatoria.',
            'password.confirmed' => 'Le password non coincidono.',
            'password.min'       => 'La password deve essere di almeno 8 caratteri.',
            'password.mixed_case'=> 'La password deve contenere maiuscole e minuscole.',
            'password.numbers'   => 'La password deve contenere almeno un numero.',
            'password.symbols'   => 'La password deve contenere almeno un carattere speciale.',
        ]);

        $user = User::create([
            'nickname' => $request->nickname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Invia email di verifica
        EmailVerificationController::sendVerificationEmail($user);

        // Login automatico dopo registrazione
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
