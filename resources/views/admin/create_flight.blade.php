<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aggiungi Volo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/create_flight.css') }}">

</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Aggiungi un nuovo volo</h2>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="{{ route('admin.flights.store') }}">
                @csrf

                <!-- Modello aereo -->
                <div class="mb-3">
                    <label for="airplane_model_id" class="form-label">Modello aereo</label>
                    <select name="airplane_model_id" id="airplane_model_id" class="form-select" required>
                        <option value="">Seleziona modello</option>
                        @foreach ($airplaneModels as $model)
                            <option value="{{ $model->id }}" {{ old('airplane_model_id') == $model->id ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('airplane_model_id')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Aeroporto di partenza -->
                <div class="mb-3">
                    <label for="departure_airport_id" class="form-label">Aeroporto di partenza</label>
                    <select name="departure_airport_id" id="departure_airport_id" class="form-select" required>
                        <option value="">Seleziona aeroporto</option>
                        @foreach ($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('departure_airport_id') == $airport->id ? 'selected' : '' }}>
                                {{ $airport->name }} ({{ $airport->city }}, {{ $airport->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('departure_airport_id')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Aeroporto di arrivo -->
                <div class="mb-3">
                    <label for="arrival_airport_id" class="form-label">Aeroporto di arrivo</label>
                    <select name="arrival_airport_id" id="arrival_airport_id" class="form-select" required>
                        <option value="">Seleziona aeroporto</option>
                        @foreach ($airports as $airport)
                            <option value="{{ $airport->id }}" {{ old('arrival_airport_id') == $airport->id ? 'selected' : '' }}>
                                {{ $airport->name }} ({{ $airport->city }}, {{ $airport->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('arrival_airport_id')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Orario di partenza -->
                <div class="mb-3">
                    <label for="departure_time" class="form-label">Orario di partenza</label>
                    <input type="datetime-local" name="departure_time" id="departure_time" value="{{ old('departure_time') }}" class="form-control" required>
                    @error('departure_time')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Orario di arrivo -->
                <div class="mb-3">
                    <label for="arrival_time" class="form-label">Orario di arrivo</label>
                    <input type="datetime-local" name="arrival_time" id="arrival_time" value="{{ old('arrival_time') }}" class="form-control" required>
                    @error('arrival_time')
                    <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Pulsanti -->
                <div class="text-center mt-4">
                    <a href="{{ route('admin.flights') }}" class="btn btn-light border">
                        <i class="bi bi-arrow-return-left me-1"></i> Annulla
                    </a>
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-plus-circle me-1"></i> Aggiungi volo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@include("footer")

</body>
</html>
