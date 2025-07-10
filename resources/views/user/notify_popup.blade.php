<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

@if(Auth::check())
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        const userId = {{ Auth::id() }};
        const socket = io('http://localhost:3000', {
            query: {
                user_id: userId
            }
        });

        socket.on('notification', (flightIds) => {
            for (const flightId of flightIds) {
                showLandingToast(flightId);
            }
        });

        function showLandingToast(flightId) {
            const container = document.querySelector('.toast-container');

            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-bg-success border-0 mb-2';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <a href="/flights/${flightId}" class="text-white text-decoration-none">
                    Il volo #${flightId} è atterrato.
                </a>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Chiudi"></button>
        </div>
    `;

            container.appendChild(toast);

            const bootstrapToast = new bootstrap.Toast(toast);
            bootstrapToast.show();

            setTimeout(() => toast.remove(), 5000);
        }
    </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="http://localhost:3000/socket.io/socket.io.js"></script>
