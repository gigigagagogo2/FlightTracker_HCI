<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifica email - FlightTracker</title>
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
                    <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" stroke="#f59e0b" stroke-width="1.5"/>
                    <path d="M2 6l10 7 10-7" stroke="#22d3ee" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>

            <h1 class="auth-title">Verifica la tua email</h1>
            <p class="auth-sub">Abbiamo inviato un link di verifica a <strong style="color:var(--text-primary)">{{ auth()->user()->email }}</strong>. Clicca il link per attivare il tuo account.</p>

            @if(session('status'))
                <div class="auth-status-msg">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="9" stroke="#22d3ee" stroke-width="1.5"/>
                        <path d="M8 12l3 3 5-5" stroke="#22d3ee" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="auth-status-msg" style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:var(--error);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="9" stroke="#ef4444" stroke-width="1.5"/>
                        <path d="M12 8v4M12 16h.01" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="auth-btn">Reinvia email di verifica</button>
            </form>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:0;">
                @csrf
                <button type="submit" class="auth-btn-ghost">Esci dall'account</button>
            </form>

        </div>
    </div>
</main>

@include("footer")
</body>
</html>
