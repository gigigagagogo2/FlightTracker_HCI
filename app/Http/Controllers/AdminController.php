<?php

namespace App\Http\Controllers;
use App\Models\AirplaneModel;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


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

    public function airports()
    {
        $airports = Airport::all();
        return view('admin/manage_airports', compact('airports'));
    }

    public function deleteAirport(Airport $airport)
    {
        $airport->delete();

        return redirect()->route('admin.airports')->with('success', 'Aeroporto eliminato con successo.');
    }

    public function createAirport() {
        return view('admin.create_airport');
    }

    public function storeAirport(Request $request) {

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'city'      => 'required|string|max:255',
            'country'   => 'required|string|max:255',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);


        // Capitalizza città e paese
        $validated['city'] = ucfirst(strtolower(trim($validated['city'])));
        $validated['country'] = ucfirst(strtolower(trim($validated['country'])));

        // Ricostruisci il nome in automatico (così sei sicuro che sia coerente)
        $validated['name'] = "Aeroporto di " . $validated['city'];
        Airport::create($validated);

        return redirect()->route('admin.airports')->with('success', 'Aeroporto aggiunto con successo.');
    }
    public function editAirport(Airport $airport)
    {
        return view('admin.edit_airport', compact('airport'));
    }

    public function updateAirport(Request $request, Airport $airport)
    {
        $validated = $request->validate([
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $validated['city'] = ucfirst(strtolower(trim($validated['city'])));
        $validated['country'] = ucfirst(strtolower(trim($validated['country'])));
        $validated['name'] = "Aeroporto di " . $validated['city'];

        $airport->update($validated);

        return redirect()->route('admin.airports')->with('success', 'Aeroporto aggiornato con successo.');
    }



    public function lookupCity(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'country' => 'required|string'
        ]);

        $city = ucfirst(strtolower(trim($request->input('city'))));
        $country = ucfirst(strtolower(trim($request->input('country'))));

        $query = urlencode("$city, $country");
        $apiKey = env('GOOGLE_MAPS_API');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$query}&language=it&key={$apiKey}";
        $response = Http::get($url);
        $json = $response->json();

        if ($json['status'] === 'OK' && isset($json['results'][0])) {
            $result = $json['results'][0];
            $components = collect($result['address_components']);

            // Prendi la country effettivamente riconosciuta
            $countryComponent = $components->first(fn($c) => in_array('country', $c['types']));
            $cityComponent = $components->first(fn($c) =>
                in_array('locality', $c['types']) ||
                in_array('administrative_area_level_2', $c['types']) ||
                in_array('administrative_area_level_1', $c['types'])
            );


            // Se la città non è stata trovata in modo esplicito, blocca
            if (!$cityComponent) {
                return response()->json([
                    'success' => false,
                    'message' => "La città non è stata trovata. Verifica che esista realmente.",
                    'invalid_country' => false
                ]);
            }

            $inputCity = strtolower(trim($request->input('city')));
            $foundCity = strtolower($cityComponent['long_name']);
            if ($inputCity !== $this->normalize($foundCity)) {
                return response()->json([
                    'success' => false,
                    'message' => "Hai scritto la città in modo errato. Intendevi forse \"$foundCity\"?",
                    'invalid_country' => false
                ]);
            }

            $normalizedRequested = $this->normalize($country);
            $normalizedFound = $this->normalize($countryComponent['long_name'] ?? '');

            if ($normalizedRequested !== $normalizedFound) {
                return response()->json([
                    'success' => false,
                    'message' => "La città non appartiene al paese indicato.",
                    'invalid_country' => true
                ]);
            }
// ...

            return response()->json([
                'success' => true,
                'city' => $result['formatted_address'],
                'latitude' => $result['geometry']['location']['lat'],
                'longitude' => $result['geometry']['location']['lng']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Città o paese non trovati. Verifica la correttezza dei dati inseriti.',
            'invalid_country' => false
        ]);
    }
    private function normalize($string) {
        return strtolower(trim(Str::ascii($string)));
    }



}

