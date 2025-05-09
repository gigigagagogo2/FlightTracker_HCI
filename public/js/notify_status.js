
document.addEventListener("DOMContentLoaded", () => {
    // Se non c'è la variabile globale userFlights definita, esce
    if (typeof userFlights === "undefined" || !Array.isArray(userFlights)) return;

    const notifiedFlights = [];

    function checkFlightsLanding() {
        userFlights.forEach(id => {
            if (notifiedFlights.includes(id)) return;

            fetch(`/api/simulazione-volo/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.stato === 'Atterrato') {
                        showLandingToast(id, data);
                        notifiedFlights.push(id);
                    }
                })
                .catch(err => console.error("Errore controllo atterraggio:", err));
        });
    }

    function showLandingToast(flightId, data) {
        const container = document.querySelector('.toast-container');

        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-success border-0 mb-2';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                Il volo #${flightId} è atterrato a ${data.arrival_city}.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Chiudi"></button>
        </div>
    `;

        container.appendChild(toast);

        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();

        // Rimuovi il toast dopo 5 secondi
        setTimeout(() => toast.remove(), 6000);
    }


    // Avvia il controllo ogni 10 secondi
    setInterval(checkFlightsLanding, 1000);
});
