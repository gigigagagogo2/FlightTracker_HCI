<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accedi - FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/access.css') }}">
</head>
<body class="page">

@include("navbar")

<main class="main-content">
    <div class="auth-container">

        <div class="auth-card">

            <!-- Badge -->
            <div class="auth-badge">
                <span class="auth-pulse"></span>
                FlightTracker
            </div>

            <!-- Icona -->
            <div class="auth-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12z" stroke="#f59e0b" stroke-width="1.5"/>
                    <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>

            <h1 class="auth-title">Bentornato</h1>
            <p class="auth-sub">Accedi al tuo account FlightTracker</p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="auth-field">
                    <label for="email" class="auth-label">Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           class="auth-input {{ $errors->has('email') ? 'auth-input--error' : '' }}"
                           autocomplete="off" required autofocus
                           placeholder="La tua email">
                    @error('email')
                    <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <div class="auth-label-row">
                        <label for="password" class="auth-label">Password</label>
                        <a href="{{ route('password.request') }}" class="auth-forgot">Password dimenticata?</a>
                    </div>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" id="password"
                               class="auth-input"
                               autocomplete="off" required
                               placeholder="••••••••">
                        <button type="button" class="auth-eye" onclick="togglePassword()">
                            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="#475569" stroke-width="1.5"/>
                                <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#475569" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Ricordami -->
                <div class="auth-remember">
                    <input type="checkbox" name="remember" id="remember" class="auth-checkbox">
                    <label for="remember" class="auth-checkbox-label">Ricordami</label>
                </div>

                <button type="submit" class="auth-btn">Accedi</button>

                <p class="auth-link">
                    Non hai ancora un account?
                    <a href="{{ route('register.form') }}">Registrati</a>
                </p>
            </form>
        </div>
    </div>
</main>

@include("footer")

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
