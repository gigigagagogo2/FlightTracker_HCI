<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Area Personale – FlightTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/personal_area.css') }}">
</head>
<body>

@include("navbar")

<main class="profile-main">
    <div class="profile-wrap">

        <!-- Solo alert success/error di sessione, NON errori di validazione campi -->
        @if(session('success'))
            <div class="profile-alert profile-alert--success">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="profile-alert profile-alert--error">
                <i class="bi bi-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @error('profile_picture')
        <div class="profile-alert profile-alert--error">
            <i class="bi bi-exclamation-circle"></i>
            {{ $message }}
        </div>
        @enderror

        <!-- Header -->
        <div class="profile-header">
            <div class="profile-badge">
                <span class="pulse-dot pulse-dot--sm"></span>
                Area personale
            </div>
            <h1 class="profile-title">Il tuo profilo</h1>
            <p class="profile-sub">Gestisci le informazioni del tuo account FlightTracker</p>
        </div>

        <div class="profile-layout">

            <!-- LEFT: avatar -->
            <div class="profile-avatar-col">
                <div class="avatar-card">
                    <div class="avatar-content">
                        <form id="pictureForm" action="{{ route('user.updatePicture') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label for="profile_picture" class="avatar-label">
                                <div class="avatar-circle">
                                    <img
                                        src="{{ $user->profile_picture_path
                                            ? route('profile.picture', ['filename' => $user->profile_picture_path])
                                            : asset('images/default_user.jpg')
                                        }}"
                                        alt="Foto profilo"
                                    >
                                    <div class="avatar-overlay">
                                        <i class="bi bi-camera"></i>
                                        <span>Cambia foto</span>
                                    </div>
                                </div>
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture"
                                   onchange="document.getElementById('pictureForm').submit();">
                        </form>

                        <div class="avatar-name">{{ Auth::user()->nickname }}</div>
                        <div class="avatar-email">{{ Auth::user()->email }}</div>

                        <div class="avatar-links">
                            <a href="{{ route('user.flights') }}" class="avatar-link">
                                <i class="bi bi-airplane"></i> I miei voli
                            </a>
                            <a href="{{ route('user.personal.map') }}" class="avatar-link">
                                <i class="bi bi-map"></i> La mia mappa
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: form -->
            <div class="profile-form-col">
                <div class="profile-form-card">
                    <div class="profile-form-card__header">
                        <span class="profile-form-card__title">Dati account</span>
                        <span class="profile-form-card__hint" id="edit-hint">Clicca su Modifica per aggiornare i dati</span>
                    </div>

                    <form id="profileForm" action="{{ route('user.updateProfile') }}" method="POST">
                        @csrf

                        <!-- Nickname -->
                        <div class="profile-field">
                            <label class="profile-label">Nome utente</label>
                            <div class="profile-input-wrap">
                                <i class="bi bi-person profile-input-icon"></i>
                                <input type="text" name="nickname" id="profile-nickname"
                                       value="{{ old('nickname', Auth::user()->nickname) }}"
                                       class="profile-input {{ $errors->has('nickname') ? 'profile-input--error' : '' }}"
                                       readonly>
                            </div>
                            @error('nickname')
                            <span class="profile-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="profile-field">
                            <label class="profile-label">Email</label>
                            <div class="profile-input-wrap">
                                <i class="bi bi-envelope profile-input-icon"></i>
                                <input type="email" name="email" id="profile-email"
                                       value="{{ old('email', Auth::user()->email) }}"
                                       class="profile-input {{ $errors->has('email') ? 'profile-input--error' : '' }}"
                                       readonly>
                            </div>
                            @error('email')
                            <span class="profile-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="profile-field">
                            <label class="profile-label">Nuova password <span class="profile-label__optional">(lascia vuoto per non cambiarla)</span></label>
                            <div class="profile-input-wrap profile-input-wrap--pw">
                                <i class="bi bi-lock profile-input-icon"></i>
                                <input type="password" name="password" id="profile-password"
                                       class="profile-input {{ $errors->has('password') ? 'profile-input--error' : '' }}"
                                       autocomplete="new-password"
                                       readonly>

                                <!-- Popup regole password -->
                                <div class="pw-popup" id="profile-pw-popup" style="display:none;">
                                    <div class="pw-popup__strength">
                                        <div class="pw-popup__track">
                                            <div class="pw-popup__fill" id="profile-strength-bar"></div>
                                        </div>
                                        <span class="pw-popup__label" id="profile-strength-label"></span>
                                    </div>
                                    <ul class="pw-popup__rules">
                                        <li class="rule" id="profile-rule-length"><span class="rule-icon"></span> Almeno 8 caratteri</li>
                                        <li class="rule" id="profile-rule-uppercase"><span class="rule-icon"></span> Almeno una maiuscola</li>
                                        <li class="rule" id="profile-rule-lowercase"><span class="rule-icon"></span> Almeno una minuscola</li>
                                        <li class="rule" id="profile-rule-number"><span class="rule-icon"></span> Almeno un numero</li>
                                        <li class="rule" id="profile-rule-special"><span class="rule-icon"></span> Almeno un carattere speciale</li>
                                    </ul>
                                </div>
                            </div><!-- end profile-input-wrap--pw -->

                            @error('password')
                            <span class="profile-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="profile-actions">
                            <button type="button" class="profile-btn profile-btn--edit" id="editBtn" onclick="enableEdit()">
                                <i class="bi bi-pencil"></i> Modifica
                            </button>
                            <button type="submit" class="profile-btn profile-btn--save" id="saveBtn" style="display:none;" disabled>
                                <i class="bi bi-check-lg"></i> Salva modifiche
                            </button>
                            <button type="button" class="profile-btn profile-btn--cancel" id="cancelBtn" style="display:none;" onclick="cancelEdit()">
                                Annulla
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

@include("footer")

<script>
    const emailRegex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;

    function checkProfileForm() {
        const nickname = document.getElementById('profile-nickname').value.trim();
        const email    = document.getElementById('profile-email').value.trim();
        const saveBtn  = document.getElementById('saveBtn');
        saveBtn.disabled = !(nickname && emailRegex.test(email));
    }

    function enableEdit() {
        document.querySelectorAll('.profile-input').forEach(i => i.removeAttribute('readonly'));
        document.getElementById('editBtn').style.display   = 'none';
        document.getElementById('saveBtn').style.display   = 'inline-flex';
        document.getElementById('cancelBtn').style.display = 'inline-flex';
        document.getElementById('edit-hint').textContent   = 'Modifica i campi e premi Salva';
        checkProfileForm();
    }

    function cancelEdit() {
        document.querySelectorAll('.profile-input').forEach(i => {
            i.setAttribute('readonly', true);
            i.value = i.defaultValue;
            i.classList.remove('profile-input--error');
        });
        document.querySelectorAll('.profile-field-error').forEach(el => el.style.display = 'none');
        document.getElementById('editBtn').style.display   = 'inline-flex';
        document.getElementById('saveBtn').style.display   = 'none';
        document.getElementById('cancelBtn').style.display = 'none';
        document.getElementById('edit-hint').textContent   = 'Clicca su Modifica per aggiornare i dati';
        // reset strength
        document.getElementById('profile-pw-popup').style.display = 'none';
        document.getElementById('profile-strength-bar').style.width = '0%';
        document.getElementById('profile-strength-label').textContent = '';
    }

    // Rimuovi errore mentre si digita
    document.querySelectorAll('.profile-input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('profile-input--error');
            const errorEl = input.closest('.profile-field')?.querySelector('.profile-field-error');
            if (errorEl) errorEl.style.display = 'none';
        });
    });

    // Check form su ogni input
    document.getElementById('profile-nickname').addEventListener('input', checkProfileForm);
    document.getElementById('profile-email').addEventListener('input', checkProfileForm);

    // ── PASSWORD STRENGTH ──
    const profilePwInput       = document.getElementById('profile-password');
    const profileStrengthBar   = document.getElementById('profile-strength-bar');
    const profileStrengthLabel = document.getElementById('profile-strength-label');

    const profileRules = {
        length:    { el: document.getElementById('profile-rule-length'),    test: v => v.length >= 8 },
        uppercase: { el: document.getElementById('profile-rule-uppercase'), test: v => /[A-Z]/.test(v) },
        lowercase: { el: document.getElementById('profile-rule-lowercase'), test: v => /[a-z]/.test(v) },
        number:    { el: document.getElementById('profile-rule-number'),    test: v => /[0-9]/.test(v) },
        special:   { el: document.getElementById('profile-rule-special'),   test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const profileLevels = [
        { label: 'Molto debole', color: '#ef4444', width: '20%' },
        { label: 'Debole',       color: '#f97316', width: '40%' },
        { label: 'Discreta',     color: '#f59e0b', width: '60%' },
        { label: 'Forte',        color: '#22d3ee', width: '80%' },
        { label: 'Ottima',       color: '#10b981', width: '100%' },
    ];

    profilePwInput.addEventListener('input', () => {
        const val = profilePwInput.value;
        document.getElementById('profile-pw-popup').style.display = val.length > 0 ? 'block' : 'none';


        let passed = 0;
        Object.values(profileRules).forEach(rule => {
            const ok = rule.test(val);
            rule.el.classList.toggle('rule--ok', ok);
            if (ok) passed++;
        });

        if (val.length === 0) {
            profileStrengthBar.style.width = '0%';
            profileStrengthLabel.textContent = '';
            return;
        }

        const level = profileLevels[Math.min(passed - 1, 4)];
        profileStrengthBar.style.width      = level.width;
        profileStrengthBar.style.background = level.color;
        profileStrengthLabel.textContent    = level.label;
        profileStrengthLabel.style.color    = level.color;
    });

    // Auto-dismiss alerts di sessione
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.profile-alert').forEach(el => {
            setTimeout(() => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            }, 3000);
        });

        // Se ci sono errori di validazione, apri automaticamente la modalità modifica
        @if($errors->any())
        enableEdit();
        @endif
    });
</script>

</body>
</html>
