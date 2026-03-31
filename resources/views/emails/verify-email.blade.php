<!DOCTYPE html>
<html lang="it">
<body style="font-family:'Segoe UI',sans-serif;padding:2rem;background:#f3f4f6;margin:0;">
<div style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
    <div style="height:4px;background:linear-gradient(90deg,#f59e0b,#22d3ee);"></div>
    <div style="padding:2rem;">
        <h2 style="color:#0a0f1e;font-size:1.4rem;margin:0 0 0.5rem;">Verifica la tua email</h2>
        <p style="color:#475569;margin:0 0 1.5rem;line-height:1.6;">Clicca il bottone qui sotto per verificare il tuo account FlightTracker. Il link scade tra <strong>60 minuti</strong>.</p>
        <a href="{{ $verifyUrl }}"
           style="display:inline-block;background:#f59e0b;color:#0a0f1e;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:0.95rem;">
            Verifica email
        </a>
        <p style="color:#94a3b8;font-size:0.8rem;margin:1.5rem 0 0;line-height:1.5;">
            Se non hai creato un account su FlightTracker, puoi ignorare questa email.
        </p>
    </div>
</div>
</body>
</html>
