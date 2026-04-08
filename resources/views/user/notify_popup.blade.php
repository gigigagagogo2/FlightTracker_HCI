@if(Auth::check())
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

<style>
.ft-notif-stack {
    position: fixed;
    top: 72px; /* sotto la navbar */
    right: 1rem;
    z-index: 2000;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    pointer-events: none;
}

.ft-notif {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: rgba(17, 24, 39, 0.96);
    border: 1px solid rgba(255,255,255,0.1);
    border-left: 3px solid #22d3ee;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    min-width: 280px;
    max-width: 340px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    pointer-events: all;
    opacity: 0;
    transform: translateX(20px);
    transition: opacity 0.25s ease, transform 0.25s ease;
    position: relative;
    overflow: hidden;
}

.ft-notif.is-visible {
    opacity: 1;
    transform: translateX(0);
}

.ft-notif.is-leaving {
    opacity: 0;
    transform: translateX(20px);
}

/* Barra progress che si svuota */
.ft-notif__progress {
    position: absolute;
    bottom: 0; left: 0;
    height: 2px;
    background: #22d3ee;
    width: 100%;
    transform-origin: left;
    animation: notif-drain 5s linear forwards;
}

@keyframes notif-drain {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}

.ft-notif__icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(34, 211, 238, 0.12);
    border: 1px solid rgba(34, 211, 238, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.85rem;
    color: #22d3ee;
    margin-top: 1px;
}

.ft-notif__body { flex: 1; min-width: 0; }

.ft-notif__label {
    font-family: 'DM Mono', monospace;
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #22d3ee;
    margin-bottom: 3px;
}

.ft-notif__route {
    font-family: 'Syne', sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
    color: #f1f5f9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ft-notif__model {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 2px;
}

.ft-notif__close {
    background: none;
    border: none;
    color: #475569;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    font-size: 1rem;
    flex-shrink: 0;
    transition: color 0.15s ease;
    margin-top: 1px;
}

.ft-notif__close:hover { color: #94a3b8; }
</style>

<div class="ft-notif-stack" id="ftNotifStack" aria-live="polite"></div>

<script>
    const userId = {{ Auth::id() }};
    const socket = io('http://localhost:3000', { query: { user_id: userId } });

    socket.on('notification', (flightIds) => {
        for (const flightId of flightIds) {
            showLandingNotif(flightId);
        }
    });

    async function showLandingNotif(flightId) {
        // Fetch info volo (da/per/modello)
        let from = '—', to = '—', model = '';
        try {
            const res = await fetch(`/api/flight-info/${flightId}`);
            if (res.ok) {
                const data = await res.json();
                from  = data.from  ?? from;
                to    = data.to    ?? to;
                model = data.model ?? model;
            }
        } catch (_) {}

        const stack = document.getElementById('ftNotifStack');

        const notif = document.createElement('a');
        notif.href  = `/flights/${flightId}`;
        notif.style.textDecoration = 'none';
        notif.className = 'ft-notif';
        notif.setAttribute('role', 'alert');
        notif.innerHTML = `
            <div class="ft-notif__icon" aria-hidden="true">✈</div>
            <div class="ft-notif__body">
                <p class="ft-notif__label">Volo atterrato</p>
                <p class="ft-notif__route">${from} → ${to}</p>
                ${model ? `<p class="ft-notif__model">${model}</p>` : ''}
            </div>
            <button class="ft-notif__close" aria-label="Chiudi notifica">✕</button>
            <div class="ft-notif__progress" aria-hidden="true"></div>
        `;

        // Chiudi al click sul tasto ×
        notif.querySelector('.ft-notif__close').addEventListener('click', (e) => {
            e.preventDefault();
            dismiss(notif);
        });

        stack.appendChild(notif);

        // Anima entrata
        requestAnimationFrame(() => notif.classList.add('is-visible'));

        // Auto-dismiss dopo 5 s
        setTimeout(() => dismiss(notif), 5000);
    }

    function dismiss(notif) {
        notif.classList.add('is-leaving');
        setTimeout(() => notif.remove(), 280);
    }
</script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="http://localhost:3000/socket.io/socket.io.js"></script>
