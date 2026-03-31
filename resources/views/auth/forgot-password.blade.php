<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password dimenticata - FlightTracker</title>
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

            <h1 class="auth-title">Password dimenticata</h1>
            <p class="auth-sub">Inserisci la tua email e ti invieremo un link per reimpostare la password.</p>

            @if(session('status'))
                <div class="auth-status-msg">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="9" stroke="#22d3ee" stroke-width="1.5"/>
                        <path d="M8 12l3 3 5-5" stroke="#22d3ee" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

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

                <button type="submit" id="forgot-btn" class="auth-btn">Invia link di recupero</button>

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
    const forgotBtn = document.getElementById('forgot-btn');
    const emailInput = document.getElementById('email');
    const emailRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

    function checkForgotForm() {
        forgotBtn.disabled = !emailRegex.test(emailInput.value.trim());
    }

    emailInput.addEventListener('input', () => {
        checkForgotForm();
        emailInput.classList.remove('auth-input--error');
        const errorEl = emailInput.closest('.auth-field')?.querySelector('.auth-error');
        if (errorEl) errorEl.style.display = 'none';
    });

    checkForgotForm();
</script>
</body>
</html>
