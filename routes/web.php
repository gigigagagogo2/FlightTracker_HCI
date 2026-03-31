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
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/flights/vicino', [HomeController::class, 'vicino'])->name('flights.vicino');
Route::get('/search-flights', [FlightController::class, 'search'])->name('flights.search');
Route::get('/flights/{id}', [FlightController::class, 'show'])->name('flights.show');
Route::post('/api/simulazione-voli', [FlightSimulationController::class, 'getMultipleFlightData']);
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/about', 'about')->name('about');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('login'))->name('login.form');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
    Route::get('/register', fn() => view('register'))->name('register.form');
    Route::post('/register', [RegisterController::class, 'store'])->name('register');
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', function (Request $request) {
    $user = Auth::user();
    if ($user) {
        $user->setRememberToken(null);
        $user->save();
    }
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('success', 'Sei uscito correttamente.');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('/profile/update-picture', [UserController::class, 'updatePicture'])->name('user.updatePicture');
    Route::get('/user/flights', [UserController::class, 'myFlights'])->name('user.flights');
    Route::get('/personalmap', [UserController::class, 'showMap'])->name('user.personal.map');
    Route::get('/profile-picture/{filename}', [UserController::class, 'showProfilePicture'])->name('profile.picture');
    Route::post('/flights/preferiti/{id}', [FlightController::class, 'aggiungiPreferito']);
    Route::delete('/flights/preferiti/{id}', [FlightController::class, 'rimuoviPreferito']);
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users/data', [AdminController::class, 'usersData'])->name('admin.users.data');
    Route::get('/flights/data', [AdminController::class, 'flightsData'])->name('admin.flights.data');
    Route::get('/airports/data', [AdminController::class, 'airportsData'])->name('admin.airports.data');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::get('/flights', [AdminController::class, 'flights'])->name('admin.flights');
    Route::get('/flights/create', [AdminController::class, 'createFlight'])->name('admin.flights.create');
    Route::post('/flights', [AdminController::class, 'storeFlight'])->name('admin.flights.store');
    Route::get('/flights/{flight}/edit', [AdminController::class, 'editFlight'])->name('admin.flights.edit');
    Route::put('/flights/{flight}', [AdminController::class, 'updateFlight'])->name('admin.flights.update');
    Route::delete('/flights/{flight}', [AdminController::class, 'deleteFlight'])->name('admin.flights.delete');
    Route::get('/airports', [AdminController::class, 'airports'])->name('admin.airports');
    Route::get('/airports/create', [AdminController::class, 'createAirport'])->name('admin.airport.create');
    Route::post('/airports', [AdminController::class, 'storeAirport'])->name('admin.airport.store');
    Route::get('/airports/{airport}/edit', [AdminController::class, 'editAirport'])->name('admin.airport.edit');
    Route::put('/airports/{airport}', [AdminController::class, 'updateAirport'])->name('admin.airport.update');
    Route::delete('/airports/{airport}', [AdminController::class, 'deleteAirport'])->name('admin.airport.delete');
    Route::post('/city-lookup', [AdminController::class, 'lookupCity'])->name('admin.city.lookup');
});
