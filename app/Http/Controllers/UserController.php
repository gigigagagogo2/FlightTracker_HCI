<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function showProfile()
    {
        return view('user/personal_area', [
            'user' => Auth::user(),
        ]);
    }

    public function showProfilePicture($filename)
    {
        $user = Auth::user();

        // Un user puo vedere solo la sua foto
        if ($user->profile_picture_path !== $filename) {
            abort(403);
        }

        $path = storage_path('app/private/profiles/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
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

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        Log::info("diocane");
        $user = Auth::user();
        Log::info("diocane1");
        $baseName = $user->nickname . '_picture';
        Log::info("diocane2");



        // Elimina tutte le vecchie immagini con lo stesso nome base
        $files = Storage::files('profiles');
        foreach ($files as $file) {
            if (str_starts_with(basename($file), $baseName)) {
                Storage::delete($file);
            }
        }

        // Salva la nuova immagine
        $extension = $request->file('profile_picture')->getClientOriginalExtension();
        $filename = $baseName . '.' . $extension;
        $request->file('profile_picture')->storeAs('profiles', $filename);

        // Aggiorna il percorso
        $user->profile_picture_path = $filename;
        $user->save();

        return redirect()->route('user.profile');
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

