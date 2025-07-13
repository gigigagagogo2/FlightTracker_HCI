<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Voli</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/manage_flights.css') }}">
</head>
<body class="bg-light">
@include("navbar")

<div class="container py-5">

    <!-- Titolo centrato -->
    <h2 class="text-center mb-4">Voli registrati</h2>

    <!-- Bottone "Aggiungi volo" centrato -->
    <div class="text-center mb-4">
        <a href="{{ route('admin.flights.create') }}" class="btn btn-success btn-sm px-3 py-2 d-inline-flex align-items-center">
            <i class="bi bi-plus-circle me-1"></i> Aggiungi volo
        </a>
    </div>


    <!-- Tabella dei voli -->
    <table class="table table-bordered table-hover bg-white rounded shadow-sm">
        <thead class="table-light text-center">
        <tr>
            <th>ID</th>
            <th>Modello Aereo</th>
            <th>Aeroporto Partenza</th>
            <th>Aeroporto Arrivo</th>
            <th>Partenza</th>
            <th>Arrivo</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        @foreach ($flights as $flight)
            <tr>
                <td>{{ $flight->id }}</td>
                <td>{{ $flight->airplaneModel->name ?? '—' }}</td>
                <td>{{ $flight->departureAirport->name ?? '—' }}</td>
                <td>{{ $flight->arrivalAirport->name ?? '—' }}</td>
                <td>{{ $flight->departure_time }}</td>
                <td>{{ $flight->arrival_time ?? '–' }}</td>
                <td>
                    <a href="{{ route('admin.flights.edit', $flight->id) }}" class="btn btn-sm btn-warning me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.flights.delete', $flight->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo volo?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>

        @endforeach
        </tbody>
    </table>


</div>

@include("footer")

</body>
</html>
