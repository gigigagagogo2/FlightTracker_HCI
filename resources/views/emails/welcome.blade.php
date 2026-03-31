<!DOCTYPE html>
<html lang="it">
<body style="font-family:'Segoe UI',sans-serif;padding:2rem;background:#f3f4f6;margin:0;">
<div style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
    <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#22d3ee);"></div>
    <div style="padding:2rem;">
        <h2 style="color:#0a0f1e;font-size:1.4rem;margin:0 0 0.5rem;">
            Benvenuto su FlightTracker, {{ $user->nickname }}!
        </h2>
        <p style="color:#475569;margin:0 0 1.5rem;line-height:1.6;">
            Il tuo account è stato verificato con successo. Puoi ora accedere a tutte le funzionalità di FlightTracker.
        </p>
        <a href="{{ url('/') }}"
           style="display:inline-block;background:#f59e0b;color:#0a0f1e;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:0.95rem;">
            Inizia a esplorare
        </a>
        <p style="color:#94a3b8;font-size:0.8rem;margin:1.5rem 0 0;line-height:1.5;">
            Grazie per esserti unito a noi!
        </p>
    </div>
</div>
</body>
</html>
