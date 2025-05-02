<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        // Validazione dei campi
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        // Tentativo di login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // protegge da session fixation
            return redirect()->intended('/')->with('success', 'Benvenuto!');
        }

        // Login fallito
        return back()->withErrors([
            'email' => 'Credenziali non valide.',
        ])->onlyInput('email');
    }
}
