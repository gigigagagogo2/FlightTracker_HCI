<?php

namespace App\Http\Controllers;
use App\Models\AirplaneModel;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin/manage_users', compact('users'));
    }

    public function deleteUser(User $user)
    {
        // Non permette di eliminare admin
        if ($user->is_admin) {
            return redirect()->route('admin.users')->with('error', 'Non puoi eliminare un admin.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utente eliminato con successo.');
    }

    public function editUser(User $user)
    {
        return view('admin.edit-user', compact('user'));
    }


    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string',
        ]);

        $user->nickname = $validated['nickname'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Utente aggiornato con successo.');
    }


    public function flights()
    {
        $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])->orderBy('departure_time')->get();
        return view('admin/manage_flights', compact('flights'));
    }

    public function createFlight()
    {
        $airports = Airport::orderBy('name')->get();
        $airplaneModels = AirplaneModel::orderBy('name')->get();

        return view('admin.create_flight', compact('airports', 'airplaneModels'));
    }

    public function storeFlight(Request $request)
    {
        // Validazione dei dati in input
        $validated = $request->validate([
            'departure_airport_id'   => 'required|exists:airports,id',
            'arrival_airport_id'     => 'required|exists:airports,id|different:departure_airport_id',
            'airplane_model_id'      => 'required|exists:airplane_models,id',
            'departure_time'         => 'required|date',
            'arrival_time'           => 'required|date|after_or_equal:departure_time',
        ]);

        // Creazione del nuovo volo
        Flight::create($validated);

        // Redirect con messaggio di successo
        return redirect()->route('admin.flights')->with('success', 'Volo aggiunto con successo.');
    }

    public function deleteFlight(Flight $flight)
    {
        $flight->delete();

        return redirect()->route('admin.flights')->with('success', 'Volo eliminato con successo.');
    }

    public function editFlight(Flight $flight)
    {
        $airports = Airport::orderBy('name')->get();
        $airplaneModels = AirplaneModel::orderBy('name')->get();

        return view('admin/edit-flight', compact('flight', 'airports', 'airplaneModels'));
    }

    public function updateFlight(Request $request, Flight $flight)
    {
        $validated = $request->validate([
            'airplane_model_id'      => 'required|exists:airplane_models,id',
            'departure_airport_id'   => 'required|exists:airports,id',
            'arrival_airport_id'     => 'required|exists:airports,id|different:departure_airport_id',
            'departure_time'         => 'required|date',
            'arrival_time'           => 'required|date|after_or_equal:departure_time',
        ]);

        $flight->update($validated);

        return redirect()->route('admin.flights')->with('success', 'Volo aggiornato con successo.');
    }

}

