<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrati - FlightTracker</title>
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

            <h1 class="auth-title">Registra un account</h1>
            <p class="auth-sub">Unisciti a FlightTracker</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Nickname -->
                <div class="auth-field">
                    <label for="nickname" class="auth-label">Nome utente</label>
                    <input type="text" name="nickname" id="nickname"
                           value="{{ old('nickname') }}"
                           class="auth-input {{ $errors->has('nickname') ? 'auth-input--error' : '' }}"
                           autocomplete="off" required autofocus
                           placeholder="Il tuo nome utente">
                    @error('nickname')
                    <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="auth-field">
                    <label for="email" class="auth-label">Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           class="auth-input {{ $errors->has('email') ? 'auth-input--error' : '' }}"
                           autocomplete="off" required
                           placeholder="La tua email">
                    @error('email')
                    <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label for="password" class="auth-label">Password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" id="password"
                               class="auth-input {{ $errors->has('password') ? 'auth-input--error' : '' }}"
                               autocomplete="off" required
                               placeholder="••••••••">
                        <button type="button" class="auth-eye" onclick="togglePassword('password')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="#475569" stroke-width="1.5"/>
                                <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#475569" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Conferma Password -->
                <div class="auth-field">
                    <label for="password_confirmation" class="auth-label">Conferma password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="auth-input"
                               autocomplete="off" required
                               placeholder="••••••••">
                        <button type="button" class="auth-eye" onclick="togglePassword('password_confirmation')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="3" stroke="#475569" stroke-width="1.5"/>
                                <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" stroke="#475569" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Registrati</button>

                <p class="auth-link">
                    Hai già un account?
                    <a href="{{ route('login.form') }}">Accedi</a>
                </p>
            </form>
        </div>
    </div>
</main>

@include("footer")

<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
