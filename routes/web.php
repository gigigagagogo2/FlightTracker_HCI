<?php

use App\Http\Controllers\FlightController;
use App\Http\Controllers\FlightSimulationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;

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

Route::get('/admin', function () {
    return view('admin/admin');
})->middleware(['auth', 'is_admin'])->name('admin.dashboard');

Route::get('/admin/users', [AdminController::class, 'users'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.users');

Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.users.delete');

Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.users.edit');

Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.users.update');

Route::get('/admin/flights', [AdminController::class, 'flights'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights');

Route::get('/admin/flights/create', [AdminController::class, 'createFlight'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights.create');

Route::post('/admin/flights', [AdminController::class, 'storeFlight'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights.store');

Route::get('/admin/flights/{flight}/edit', [AdminController::class, 'editFlight'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights.edit');

Route::put('/admin/flights/{flight}', [AdminController::class, 'updateFlight'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights.update');

Route::delete('/admin/flights/{flight}', [AdminController::class, 'deleteFlight'])
    ->middleware(['auth', 'is_admin'])
    ->name('admin.flights.delete');

Route::get('/search-flights', [FlightController::class, 'search'])->name('flights.search');

Route::get('/flights/{id}', [FlightController::class, 'show'])->name('flights.show');

Route::get('/api/simulazione-volo/{id}', [FlightSimulationController::class, 'getFlightData']);

Route::post('/flights/preferiti/add', [FlightController::class, 'aggiungiPreferito'])->middleware('auth');

Route::post('/flights/preferiti/remove', [FlightController::class, 'rimuoviPreferito'])->middleware('auth');

Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');

Route::post('/profile/update-picture', [UserController::class, 'updatePicture'])->middleware('auth')->name('user.updatePicture');

