<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuova password - FlightTracker</title>
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

            <div class="auth-badge">
                <span class="auth-pulse"></span>
                FlightTracker
            </div>

            <div class="auth-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <rect x="5" y="11" width="14" height="10" rx="2" stroke="#f59e0b" stroke-width="1.5"/>
                    <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="12" cy="16" r="1.5" fill="#22d3ee"/>
                </svg>
            </div>

            <h1 class="auth-title">Nuova password</h1>
            <p class="auth-sub">Scegli una nuova password per il tuo account.</p>

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="auth-field">
                    <label for="password" class="auth-label">Nuova password</label>
                    <div class="auth-input-wrap">
                        <input type="password" name="password" id="password"
                               class="auth-input {{ $errors->has('password') ? 'auth-input--error' : '' }}"
                               autocomplete="off" required autofocus
                               placeholder="Minimo 8 caratteri">
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

                @error('email')
                <span class="auth-error" style="display:block;margin-bottom:1rem;">{{ $message }}</span>
                @enderror

                <button type="submit" id="reset-btn" class="auth-btn">Aggiorna password</button>

                <p class="auth-link">
                    Ricordi la password?
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

    const resetBtn = document.getElementById('reset-btn');

    function checkResetForm() {
        const password = document.getElementById('password').value.trim();
        const confirm = document.getElementById('password_confirmation').value.trim();
        resetBtn.disabled = !(password.length >= 8 && confirm && password === confirm);
    }

    document.getElementById('password').addEventListener('input', checkResetForm);
    document.getElementById('password_confirmation').addEventListener('input', checkResetForm);

    document.querySelectorAll('.auth-input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('auth-input--error');
            const errorEl = input.closest('.auth-field')?.querySelector('.auth-error');
            if (errorEl) errorEl.style.display = 'none';
        });
    });

    checkResetForm();
</script>
</body>
</html>
