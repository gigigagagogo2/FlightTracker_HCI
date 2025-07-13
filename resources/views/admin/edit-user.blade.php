<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifica Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/edit-user.css') }}">

</head>
<body class="bg-light">
@include("navbar")

<div class="container py-5">
    <h2 class="mb-4 text-center">Modifica Utente</h2>

    <div class="card p-4 shadow-sm border rounded-3">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nickname" class="form-label">Nickname</label>
                <input type="text" class="form-control rounded-2" id="nickname" name="nickname" value="{{ old('nickname', $user->nickname) }}" required>
                @error('nickname')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control rounded-2" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Nuova password (lascia vuoto per non cambiarla)</label>
                <input type="password" class="form-control rounded-2" id="password" name="password">
                @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('admin.users') }}" class="btn btn-danger rounded-2 me-2">
                    <i class="bi bi-arrow-return-left me-1"></i> Annulla
                </a>
                <button type="submit" class="btn btn-success rounded-2">
                    <i class="bi bi-pencil-square me-1"></i> Salva modifiche
                </button>
            </div>
        </form>
    </div>
</div>

@include("footer")

</body>

</html>
