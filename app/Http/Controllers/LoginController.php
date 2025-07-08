<?php
namespace App\Http\Controllers;

// Per accedere agli input del form
use Illuminate\Http\Request;
// Per usare il sistema di autenticazione di Laravel.
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Metodo per autenticazione che si attiva una volta che la rotta 'login' riceve un POST
     * @param Request $request Oggetto contente i dati del form
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(Request $request)
    {
        // Validazione dei campi ('email' e 'password' corrispondono agli elementi html con quel name)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);



        $remember = $request->has('remember');

        // Tentativo di login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // protegge da session fixation (attacco)
            // Se il login è riuscito ti reindirizza dove si voleva andare prima del login (intended).
            // Pagina di reindirizzamento di defaul: '/'
            return redirect()->intended('/');
        }

        // Login fallito
        return back()->withErrors([
            'email' => 'Credenziali non valide.',
        ])->onlyInput('email');
    }
}
