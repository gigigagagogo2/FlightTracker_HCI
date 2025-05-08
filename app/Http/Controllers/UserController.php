<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function showProfile()
    {
        return view('user/personal_area', [
            'user' => Auth::user(),
        ]);
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

        // Salva l'immagine nella cartella 'public/profiles' e ottieni il percorso relativo
        $imagePath = $request->file('profile_picture')->store('profiles', 'public');

        // Salva il percorso nel database
        $user->profile_picture = $imagePath;
        $user->save();

        // Restituisce la risposta con il nuovo percorso dell'immagine
        return response()->json([
            'success' => true,
            'imageUrl' => asset('storage/' . $imagePath)  // Percorso completo dell'immagine
        ]);
    }


}

