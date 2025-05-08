<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/access.css') }}">
</head>
<body class="page">

<div class="position-absolute top-0 start-0 m-3">
    <a href="{{ route('home') }}" class="btn btn-outline-primary">
        <i class="bi bi-house-door"></i> Home
    </a>
</div>

<div class="container">
    <div class="card shadow-sm">
        <h2 class="text-center mb-4">Accedi</h2>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" autocomplete="off" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" autocomplete="off" required>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label">Ricordami</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Accedi</button>

            <div class="text-center mt-3">
                <span>Non hai ancora un account?</span>
                <a href="{{ route('register.form') }}" class="text-decoration-none">Registrati</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
