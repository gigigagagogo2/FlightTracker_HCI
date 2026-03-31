<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrati - FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
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
                           placeholder="Inserisci il tuo nome utente">
                    @error('nickname')
                    <span class="auth-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="auth-field">
                    <label for="email" class="auth-label">Email</label>
                    <input type="email" name="email" id="email"
                           pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                           value="{{ old('email') }}"
                           class="auth-input {{ $errors->has('email') ? 'auth-input--error' : '' }}"
                           autocomplete="off" required
                           placeholder="Inserisci la tua email (es. mariorossi@gmail.com)">
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
                    <!-- Strength bar -->
                    <div class="strength-wrap" id="strength-wrap" style="display:none;">
                        <div class="strength-track">
                            <div class="strength-fill" id="strength-bar"></div>
                        </div>
                        <span class="strength-label" id="strength-label"></span>
                    </div>

                    <!-- Regole -->
                    <ul class="password-rules" id="password-rules" style="display:none;">
                        <li class="rule" id="rule-length">
                            <span class="rule-icon"></span> Almeno 8 caratteri
                        </li>
                        <li class="rule" id="rule-uppercase">
                            <span class="rule-icon"></span> Almeno una lettera maiuscola
                        </li>
                        <li class="rule" id="rule-lowercase">
                            <span class="rule-icon"></span> Almeno una lettera minuscola
                        </li>
                        <li class="rule" id="rule-number">
                            <span class="rule-icon"></span> Almeno un numero
                        </li>
                        <li class="rule" id="rule-special">
                            <span class="rule-icon"></span> Almeno un carattere speciale (!@#$...)
                        </li>
                    </ul>
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

                <button id="register-btn" type="submit" class="auth-btn">Registrati</button>

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
    const passwordInput = document.getElementById('password');
    const strengthBar    = document.getElementById('strength-bar');
    const strengthLabel  = document.getElementById('strength-label');
    const rules = {
        length:    { el: document.getElementById('rule-length'),    test: v => v.length >= 8 },
        uppercase: { el: document.getElementById('rule-uppercase'), test: v => /[A-Z]/.test(v) },
        lowercase: { el: document.getElementById('rule-lowercase'), test: v => /[a-z]/.test(v) },
        number:    { el: document.getElementById('rule-number'),    test: v => /[0-9]/.test(v) },
        special:   { el: document.getElementById('rule-special'),   test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const levels = [
        { label: 'Molto debole', color: '#ef4444', width: '20%' },
        { label: 'Debole',       color: '#f97316', width: '40%' },
        { label: 'Discreta',     color: '#f59e0b', width: '60%' },
        { label: 'Forte',        color: '#22d3ee', width: '80%' },
        { label: 'Ottima',       color: '#10b981', width: '100%' },
    ];

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;

        document.getElementById('password-rules').style.display = val.length > 0 ? 'flex' : 'none';
        document.getElementById('strength-wrap').style.display = val.length > 0 ? 'flex' : 'none';


        let passed = 0;

        Object.values(rules).forEach(rule => {
            const ok = rule.test(val);
            rule.el.classList.toggle('rule--ok', ok);
            if (ok) passed++;
        });

        if (val.length === 0) {
            strengthBar.style.width = '0%';
            strengthLabel.textContent = '';
            return;
        }

        const level = levels[Math.min(passed - 1, 4)];
        strengthBar.style.width = level.width;
        strengthBar.style.background = level.color;
        strengthLabel.textContent = level.label;
        strengthLabel.style.color = level.color;
    });

    function checkRegisterForm() {
        const nickname = document.getElementById('nickname').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        const confirm = document.getElementById('password_confirmation').value.trim();

        // Valida formato email con regex
        const emailRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
        const emailValida = emailRegex.test(email);

        document.getElementById('register-btn').disabled = !(nickname && emailValida && password && confirm);
    }
    checkRegisterForm();

    document.querySelectorAll('.auth-input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('auth-input--error');
            const errorEl = input.closest('.auth-field')?.querySelector('.auth-error');
            if (errorEl) errorEl.style.display = 'none';
        });

    });

    ['nickname','email','password','password_confirmation'].forEach(id => {
        document.getElementById(id).addEventListener('input', checkRegisterForm);
    });

</script>
</body>
</html>
