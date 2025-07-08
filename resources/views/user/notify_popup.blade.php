<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

@if(Auth::check())
    <script>

        @php
            $user = Auth::user();
            $notNotifiedFlights = $user->notNotifiedFlightsRelation()->pluck('id');
        @endphp

        const notNotifiedUserFlights = new Set(@json($notNotifiedFlights));

        function checkFlightsLanding() {

            notNotifiedUserFlights.forEach(id => {
                fetch(`/api/simulazione-volo/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.progress === 1) {
                            showLandingToast(id, data);

                            fetch(`/api/volo-notificato/${id}/{{ $user->id }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({})
                            });

                            notNotifiedUserFlights.delete(id);
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

            setTimeout(() => toast.remove(), 5000);
        }

        setInterval(checkFlightsLanding, 2000);
    </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
