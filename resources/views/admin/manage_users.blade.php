<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestione Utenti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/manage_users.css') }}">

</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">Utenti Registrati</h2>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Nickname</th>
            <th>Email</th>
            <th>Ruolo</th>
            <th>Azioni</th> <!-- nuova colonna -->
        </tr>
        </thead>
        <tbody>
        @foreach ($users as $user)
            @if (!$user->is_admin)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->nickname }}</td>
                    <td>{{ $user->email }}</td>
                    <td>Utente</td>
                    <td>
                        <!-- Bottone modifica -->
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <!-- Bottone elimina -->
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo utente?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

</div>

</body>
</html>
