@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>I miei voli preferiti</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/personal_area.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
@include('navbar')

<main class="main-content">
    <div class="container mt-5">
        <h2 class="text-center mb-4">I miei voli preferiti</h2>

        @if($flights->isEmpty())
            <div class="alert alert-info text-center">
                Non hai aggiunto nessun volo ai preferiti.
            </div>
        @else
            <table class="table table-bordered table-hover bg-white shadow-sm">
                <thead class="table-light text-center">
                <tr>
                    <th>ID</th>
                    <th>Partenza</th>
                    <th>Arrivo</th>
                    <th>Orario Partenza</th>
                    <th>Orario Arrivo</th>
                    <th></th>
                </tr>
                </thead>
                <tbody class="text-center align-middle">
                @foreach($flights as $flight)
                    <tr>
                        <td>{{ $flight->id }}</td>
                        <td>{{ $flight->departureAirport->city ?? '—' }}</td>
                        <td>{{ $flight->arrivalAirport->city ?? '—' }}</td>
                        <td>{{ Carbon::parse($flight->departure_time)->format('d/m/Y H:i') }}</td>
                        <td>{{ Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') }}</td>
                        <td>
                            <form action="{{ url('/flights/preferiti/' . $flight->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger remove-favorite-btn" data-flight-id="{{ $flight->id }}">
                                    <i class="bi bi-star-fill" style="text-decoration: line-through; margin-right: 5px;"></i> Rimuovi
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.remove-favorite-btn');

        buttons.forEach(button => {
        button.addEventListener('click', async (e) => {
        e.preventDefault();
        const flightId = button.getAttribute('data-flight-id');

        if (!confirm('Sei sicuro di voler rimuovere questo volo dai preferiti?')) {
        return;
    }

        try {
        const response = await fetch(`/flights/preferiti/${flightId}`, {
        method: 'DELETE',
        headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
    });

        if (response.ok) {
        // Rimuovi la riga della tabella
        const row = button.closest('tr');
        if(row) {
        row.remove();
    }
    } else {
        alert('Errore nella rimozione del volo dai preferiti.');
    }
    } catch (error) {
        alert('Errore di rete. Riprova più tardi.');
        console.error(error);
    }
    });
    });
    });
</script>

@include('footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
