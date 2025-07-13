<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Voli</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/manage_airports.css') }}">
</head>
<body class="bg-light">
@include("navbar")

<div class="container py-5">

    <!-- Titolo centrato -->
    <h2 class="text-center mb-4">Aeroporti registrati</h2>

    <!-- Bottone "Aggiungi aeroporto" centrato -->
    <div class="text-center mb-4">
        <a href="{{ route('admin.airport.create') }}" class="btn btn-success btn-sm px-3 py-2 d-inline-flex align-items-center">
            <i class="bi bi-plus-circle me-1"></i> Aggiungi aeroporto
        </a>
    </div>


    <!-- Tabella dei voli -->
    <table class="table table-bordered table-hover bg-white rounded shadow-sm">
        <thead class="table-light text-center">
        <tr>
            <th>ID</th>
            <th>Nome aeroporto</th>
            <th>Città</th>
            <th>Paese</th>
            <th>Latitudine</th>
            <th>Longitudine</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="text-center align-middle">
        @foreach ($airports as $airport)
            <tr>
                <td>{{ $airport -> id }}</td>
                <td>{{ $airport -> name ?? '—' }}</td>
                <td>{{ $airport -> city ?? '—' }}</td>
                <td>{{ $airport -> country ?? '—' }}</td>
                <td>{{ $airport -> latitude ?? '-' }}</td>
                <td>{{ $airport -> longitude ?? '–' }}</td>
                <td>
                    <a  href="{{ route('admin.airport.edit', $airport->id) }}" class="btn btn-sm btn-warning me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('admin.airport.delete', $airport->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo aeroporto?')">
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
