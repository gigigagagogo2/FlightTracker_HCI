<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifica Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-user.css') }}">

</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center">Modifica Utente</h2>

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nickname" class="form-label">Nickname</label>

                <input type="text" class="form-control" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname) }}" required>
                @error('nickname')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-end">
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">Annulla</a>
                <button type="submit" class="btn btn-primary">Salva modifiche</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
