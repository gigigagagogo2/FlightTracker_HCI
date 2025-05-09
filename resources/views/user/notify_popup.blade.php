<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>
@if(Auth::check())
    <script>
        const userFlights = @json(Auth::user()->flights->pluck('id'));
    </script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/notify_status.js') }}" defer></script>
