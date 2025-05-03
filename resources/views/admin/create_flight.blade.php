<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aggiungi Volo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/create_flight.css') }}">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Aggiungi un nuovo volo</h2>

    <div class="card p-4 shadow-sm mx-auto" style="max-width: 600px;">
        <form action="{{ route('admin.flights.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="departure_airport" class="form-label">Aeroporto di partenza</label>
                <input type="text" name="departure_airport" id="departure_airport" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="arrival_airport" class="form-label">Aeroporto di arrivo</label>
                <input type="text" name="arrival_airport" id="arrival_airport" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="departure_time" class="form-label">Orario di partenza</label>
                <input type="datetime-local" name="departure_time" id="departure_time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="arrival_time" class="form-label">Orario di arrivo (opzionale)</label>
                <input type="datetime-local" name="arrival_time" id="arrival_time" class="form-control">
            </div>

            <div class="text-end">
                <a href="{{ route('admin.flights') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-success">Salva</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
