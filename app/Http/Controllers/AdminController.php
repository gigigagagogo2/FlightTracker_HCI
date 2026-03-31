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

        public function index()
        {
            $airports       = Airport::orderBy('name')->get(); // serve per i select dei modal voli
            $airplaneModels = AirplaneModel::orderBy('name')->get(); // serve per i select dei modal voli

            return view('admin.admin', compact('airports', 'airplaneModels'));
        }

        public function users()
        {
            $users = User::orderBy('created_at', 'desc')->get();
            return view('admin/manage_users', compact('users'));
        }

        public function deleteUser(User $user)
        {
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')
                    ->withFragment('users')
                    ->with('error', 'Non puoi eliminare un admin.');
            }

            $user->delete();

            return redirect()->route('admin.dashboard')
                ->withFragment('users')
                ->with('success', 'Utente eliminato con successo.');
        }

        public function editUser(User $user)
        {
            return view('admin.edit-user', compact('user'));
        }


        public function updateUser(Request $request, User $user)
        {
            $validator = \Validator::make($request->all(), [
                'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
                'email' => 'required|email:rfc,strict|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
            ], [
                'nickname.required' => 'Il nickname è obbligatorio.',
                'nickname.unique'   => 'Questo nickname è già in uso.',
                'email.required'    => 'L\'email è obbligatoria.',
                'email.email'       => 'Inserisci un\'email valida (es. nome@dominio.it).',
                'email.unique'      => 'Questa email è già in uso.',
                'password.min'      => 'La password deve essere di almeno 8 caratteri.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('admin.dashboard')
                    ->withFragment('users')
                    ->withErrors($validator, 'editUser')
                    ->withInput();
            }

            $user->nickname = $validator->validated()['nickname'];
            $user->email    = $validator->validated()['email'];

            if (!empty($validator->validated()['password']))    {
                $user->password = Hash::make($validator->validated()['password']);
            }

            $user->save();

            return redirect()->route('admin.dashboard')
                ->withFragment('users')
                ->with('success', 'Utente aggiornato con successo.');
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
            $validator = \Validator::make($request->all(), [
                'departure_airport_id'   => 'required|exists:airports,id',
                'arrival_airport_id'     => 'required|exists:airports,id|different:departure_airport_id',
                'airplane_model_id'      => 'required|exists:airplane_models,id',
                'departure_time'         => 'required|date_format:d/m/Y H:i',
                'arrival_time'   => 'required|date_format:d/m/Y H:i|after:departure_time',
            ], [
                'arrival_airport_id.different'  => 'L\'aeroporto di arrivo deve essere diverso da quello di partenza.',
                'departure_airport_id.required' => 'Seleziona un aeroporto di partenza.',
                'arrival_airport_id.required'   => 'Seleziona un aeroporto di arrivo.',
                'airplane_model_id.required'    => 'Seleziona un modello di aereo.',
                'departure_time.required'       => 'Inserisci l\'orario di partenza.',
                'arrival_time.required'         => 'Inserisci l\'orario di arrivo.',
                'arrival_time.after' => 'L\'orario di arrivo deve essere successivo a quello di partenza.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('admin.dashboard')
                    ->withFragment('flights')
                    ->withErrors($validator, 'createFlight')
                    ->withInput()
                    ->with('_form', 'create_flight');
            }

            $data = $validator->validated();
            $data['departure_time'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['departure_time']);
            $data['arrival_time']   = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['arrival_time']);


            // Creazione del nuovo volo
            Flight::create($data);
            // Redirect con messaggio di successo
            return redirect()->route('admin.dashboard')
                ->withFragment('flights')
                ->with('success', 'Volo aggiunto con successo.');
        }


        public function deleteFlight(Flight $flight)
        {
            $flight->delete();

            return redirect()->route('admin.dashboard')
                ->withFragment('flights')
                ->with('success', 'Volo eliminato con successo.');
        }

        public function editFlight(Flight $flight)
        {
            $airports = Airport::orderBy('name')->get();
            $airplaneModels = AirplaneModel::orderBy('name')->get();

            return view('admin/edit-flight', compact('flight', 'airports', 'airplaneModels'));
        }

        public function updateFlight(Request $request, Flight $flight)
        {
            $validator = \Validator::make($request->all(), [
                'airplane_model_id'    => 'required|exists:airplane_models,id',
                'departure_airport_id' => 'required|exists:airports,id',
                'arrival_airport_id'   => 'required|exists:airports,id|different:departure_airport_id',
                'departure_time' => 'required|date_format:d/m/Y H:i',
                'arrival_time'   => 'required|date_format:d/m/Y H:i|after:departure_time',
            ], [
                'arrival_airport_id.different' => 'L\'aeroporto di arrivo deve essere diverso da quello di partenza.',
                'airplane_model_id.required'   => 'Seleziona un modello di aereo.',
                'departure_airport_id.required'=> 'Seleziona un aeroporto di partenza.',
                'arrival_airport_id.required'  => 'Seleziona un aeroporto di arrivo.',
                'departure_time.required'      => 'Inserisci l\'orario di partenza.',
                'arrival_time.required'        => 'Inserisci l\'orario di arrivo.',
                'arrival_time.after'  => 'L\'orario di arrivo deve essere successivo a quello di partenza.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('admin.dashboard')
                    ->withFragment('flights')
                    ->withErrors($validator, 'editFlight')
                    ->withInput();
            }

            $data = $validator->validated();
            $data['departure_time'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['departure_time']);
            $data['arrival_time']   = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $data['arrival_time']);


            $flight->update($data);

            return redirect()->route('admin.dashboard')
                ->withFragment('flights')
                ->with('success', 'Volo aggiornato con successo.');
        }

        public function airports()
        {
            $airports = Airport::all();
            return view('admin/manage_airports', compact('airports'));
        }

        public function deleteAirport(Airport $airport)
        {
            $airport->delete();

            return redirect()->route('admin.dashboard')
                ->withFragment('airports')
                ->with('success', 'Aeroporto eliminato con successo.');    }

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


            // Controllo duplicati
            $existsByName = Airport::where('name', $validated['name'])->exists();
            $lat = round($validated['latitude'], 6);
            $lon = round($validated['longitude'], 6);

            $existsByCoords = Airport::whereRaw('ROUND(latitude, 6) = ?', [$lat])
                ->whereRaw('ROUND(longitude, 6) = ?', [$lon])
                ->exists();


            if ($existsByName || $existsByCoords) {
                $errors = [];

                if ($existsByName) {
                    $errors['name'] = 'Esiste già un aeroporto con questo nome.';
                }

                if ($existsByCoords) {
                    $errors['city'] = 'Esiste già un aeroporto alle stesse coordinate.';
                }

                return redirect()->back()->withInput()->withErrors($errors);
            }

            $prefisso = 'Aeroporto ';
            $nome = trim($validated['name']);
            if (!Str::startsWith($nome, $prefisso)) {
                $validated['name'] = $prefisso . $nome;
            }

            Airport::create($validated);
            return redirect()->route('admin.dashboard')
                ->withFragment('airports')
                ->with('success', 'Aeroporto aggiunto con successo.');
        }
        public function editAirport(Airport $airport)
        {
            return view('admin.edit_airport', compact('airport'));
        }

        public function updateAirport(Request $request, $id) {
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

            $nome = trim($validated['name']);
            $prefisso = 'Aeroporto ';

            if (!Str::startsWith($nome, $prefisso)) {
                $validated['name'] = $prefisso . $nome;
            }

            // Arrotonda le coordinate
            $lat = round($validated['latitude'], 6);
            $lon = round($validated['longitude'], 6);

            // Controllo duplicati, escludendo l’aeroporto corrente
            $existsByName = Airport::where('name', $validated['name'])
                ->where('id', '!=', $id)
                ->exists();

            $existsByCoords = Airport::whereRaw('ROUND(latitude, 6) = ?', [$lat])
                ->whereRaw('ROUND(longitude, 6) = ?', [$lon])
                ->where('id', '!=', $id)
                ->exists();

            if ($existsByName || $existsByCoords) {
                $errors = [];

                if ($existsByName) {
                    $errors['name'] = 'Esiste già un aeroporto con questo nome.';
                }

                if ($existsByCoords) {
                    $errors['city'] = 'Esiste già un aeroporto alle stesse coordinate.';
                }

                return redirect()->back()->withInput()->withErrors($errors);
            }

            // Salva le coordinate arrotondate nel database
            $validated['latitude'] = $lat;
            $validated['longitude'] = $lon;

            $airport = Airport::findOrFail($id);
            $airport->update($validated);

            return redirect()->route('admin.dashboard')
                ->withFragment('airports')
                ->with('success', 'Aeroporto aggiornato con successo.');    }




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

        public function usersData(Request $request)
        {
            $sortable = ['id', 'nickname', 'email'];
            $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'id';
            $dir  = $request->get('dir') === 'desc' ? 'desc' : 'asc';
            $search = $request->get('search', '');

            $users = User::where('is_admin', false)
                ->when($search, fn($q) => $q->where(fn($q) =>
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                ))
                ->orderBy($sort, $dir)
                ->paginate(20);
            return response()->json($users);
        }

        public function flightsData(Request $request)
        {
            $sortable = ['flights.id', 'departure_time', 'arrival_time', 'departure_airport_id', 'arrival_airport_id', 'airplane_models.name'];
            $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'departure_time';
            $dir  = $request->get('dir') === 'desc' ? 'desc' : 'asc';
            $search = $request->get('search', '');

            $flights = Flight::with(['departureAirport', 'arrivalAirport', 'airplaneModel'])
                ->join('airplane_models', 'flights.airplane_model_id', '=', 'airplane_models.id')
                ->join('airports as dep', 'flights.departure_airport_id', '=', 'dep.id')
                ->join('airports as arr', 'flights.arrival_airport_id', '=', 'arr.id')
                ->select('flights.*')
                ->when($search, fn($q) => $q->where(fn($q) =>
                $q->where('airplane_models.name', 'like', "%{$search}%")
                    ->orWhere('dep.name', 'like', "%{$search}%")
                    ->orWhere('arr.name', 'like', "%{$search}%")
                    ->orWhereRaw("DATE_FORMAT(flights.departure_time, '%d/%m/%Y %H:%i') like ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(flights.arrival_time, '%d/%m/%Y %H:%i') like ?", ["%{$search}%"])
                ))
                ->orderBy($sort, $dir)
                ->paginate(20);

            return response()->json([
                'data' => $flights->map(fn($f) => [
                    'id'                   => $f->id,
                    'airplane_model'       => $f->airplaneModel->name ?? '—',
                    'departure_airport'    => $f->departureAirport->name ?? '—',
                    'arrival_airport'      => $f->arrivalAirport->name ?? '—',
                    'departure_time'       => \Carbon\Carbon::parse($f->departure_time)->format('d/m/Y H:i'),
                    'arrival_time'         => $f->arrival_time ? \Carbon\Carbon::parse($f->arrival_time)->format('d/m/Y H:i') : '–',
                    'airplane_model_id'    => $f->airplane_model_id,
                    'departure_airport_id' => $f->departure_airport_id,
                    'arrival_airport_id'   => $f->arrival_airport_id,
                ]),
                'current_page' => $flights->currentPage(),
                'last_page'    => $flights->lastPage(),
                'total'        => $flights->total(),
            ]);
        }

        public function airportsData(Request $request)
        {
            $sortable = ['id', 'name', 'city', 'country'];
            $sort = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'name';
            $dir  = $request->get('dir') === 'desc' ? 'desc' : 'asc';
            $search = $request->get('search', '');

            $airports = Airport::when($search, fn($q) => $q->where(fn($q) =>
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('country', 'like', "%{$search}%")
            ))
                ->orderBy($sort, $dir)
                ->paginate(20);
            return response()->json($airports);
        }


    }

