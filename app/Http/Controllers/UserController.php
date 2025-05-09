<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function showProfile()
    {
        return view('user/personal_area', [
            'user' => Auth::user(),
        ]);
    }

    public function showMap()
    {
        $flights = Auth::user()->flights()->with([
            'airplaneModel',
            'departureAirport',
            'arrivalAirport'
        ])->get();

        return view('user/personal_map', compact('flights'));
    }

    // Metodo per aggiornare l'immagine del profilo
    public function updatePicture(Request $request)
    {
        // Validazione dell'immagine
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Limita a determinati formati
        ]);

        // Recupera l'utente loggato
        $user = Auth::user();

        $filename = Auth::user()->nickname . '_picture' . '.' . $request->file('profile_picture')->getClientOriginalExtension();
        $request->file('profile_picture')->storeAs('profiles', $filename, 'public');


        // Salva il percorso nel database
        $user->profile_picture_path = 'storage/profiles/' . $filename;
        $user->save();

        // Restituisce la risposta con il nuovo percorso dell'immagine
        return redirect()->route('user.profile')->with('success', 'Immagine aggiornata con successo.');

    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->nickname = $request->input('nickname');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profilo aggiornato con successo.');
    }


}

