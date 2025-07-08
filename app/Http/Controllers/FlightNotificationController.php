<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Flight;

class FlightNotificationController extends Controller
{
    // Metodo per segnare un volo come notificato per un utente specifico
    public function markAsNotified(Request $request, $flightId, $userId): \Illuminate\Http\JsonResponse
    {
        // Trova l'utente
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Utente non trovato'], 404);
        }

        // Controlla che il volo esista tra quelli dell'utente
        if (!$user->flights()->where('flights.id', $flightId)->exists()) {
            return response()->json(['error' => 'Volo non trovato per l\'utente'], 404);
        }

        // Aggiorna la colonna 'notified' nella tabella pivot user_flight
        $user->flights()->updateExistingPivot($flightId, ['notified' => true]);

        return response()->json(['success' => true]);
    }
}
