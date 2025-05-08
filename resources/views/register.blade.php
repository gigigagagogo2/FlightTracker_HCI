<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrati - FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body class="register-page">

<div class="position-absolute top-0 start-0 m-3">
    <a href="{{ route('home') }}" class="btn btn-outline-primary">
        <i class="bi bi-house-door"></i> Home
    </a>
</div>

<div class="register-container">
    <div class="register-card shadow-sm">
        <h2 class="text-center mb-4">Crea un account</h2>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nickname" class="form-label">Nickname</label>
                <input type="text" name="nickname" id="nickname" value="{{ old('nickname') }}" class="form-control" autocomplete="off" required>
                @error('nickname')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" autocomplete="off" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="off" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Conferma password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="off" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrati</button>
        </form>
    </div>
</div>

</body>
</html>
