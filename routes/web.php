<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login.form');

Route::get('/register', function () {
    return view('register');
})->name('register.form');

Route::post('/register', [RegisterController::class, 'store']) -> name('register');

Route::post('/login', [LoginController::class, 'authenticate']) -> name('login');

Route::post('/logout', function (Request $request) {
    // Invalida anche il remember_token
    $user = Auth::user();
    if ($user) {
        $user->setRememberToken(null);
        $user->save();
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'Sei uscito correttamente.');
})->name('logout');
