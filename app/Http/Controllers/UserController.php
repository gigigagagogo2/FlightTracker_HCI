<?php
namespace App\Http\Controllers;

use App\Services\FlightSimulationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function showProfile()
    {
        return view('user/personal_area', [
            'user' => Auth::user(),
        ]);
    }

    public function myFlights()
    {
        $user = auth()->user();

        // Supponendo che tu abbia una relazione flights nell'utente
        $flights = $user->flights()->with(['departureAirport', 'arrivalAirport', 'airplaneModel'])->get();
        return view('user.my-flights', compact('flights'));
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

        $service = new FlightSimulationService();
        $flightSimData = $service->simulateMultipleFlights($flights);

        $activeFlightsCount = collect($flights)->filter(function($flight) use ($flightSimData) {
            if (!isset($flightSimData[$flight->id])) return false;

            $progress = $flightSimData[$flight->id]['progress'];
            return $progress > 0 && $progress < 1;
        })->count();


        return view('user/personal_map', compact('flights', 'activeFlightsCount'));
    }

    // Metodo per aggiornare l'immagine del profilo
    public function updatePicture(Request $request)
    {

        if (!$request->hasFile('profile_picture')) {
            return back()->with('error', 'Impossibile caricare l\'immagine selezionata');
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'profile_picture.required' => 'Seleziona un file immagine.',
            'profile_picture.image' => 'Il file caricato non è un\'immagine valida.',
            'profile_picture.mimes' => 'Il formato dell\'immagine deve essere: jpeg, png, jpg o gif.',
            'profile_picture.max' => 'L\'immagine non può superare i 2MB.',
        ]);

        $user = Auth::user();
        $baseName = $user->nickname . '_picture';

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

        return redirect()->route('user.profile')->with('success', 'Immagine del profilo aggiornata con successo.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . Auth::id(),
            'email'    => ['required', 'email:rfc,strict', 'unique:users,email,' . Auth::id()],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'nickname.required' => 'Il nickname è obbligatorio.',
            'nickname.unique'   => 'Questo nickname è già in uso.',
            'email.required'    => 'L\'email è obbligatoria.',
            'email.email'       => 'Inserisci un\'email valida.',
            'email.unique'      => 'Questa email è già registrata.',
            'password.min'      => 'La password deve essere di almeno 8 caratteri.',
            'password.mixed_case' => 'La password deve contenere maiuscole e minuscole.',
            'password.numbers'    => 'La password deve contenere almeno un numero.',
            'password.symbols'    => 'La password deve contenere almeno un carattere speciale.',
        ]);

        $user->nickname = $request->input('nickname');
        $user->email = $request->input('email');

        if ($request->filled('password') && $request->input('password') !== '********') {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profilo aggiornato con successo.');
    }

}

