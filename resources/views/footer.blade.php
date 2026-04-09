<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<footer class="site-footer {{ (auth()->check() && auth()->user()->is_admin && request()->routeIs('admin.*')) ? 'site-footer--light' : '' }}">
    <div class="footer-inner">
        <p class="footer-contact">
            <span class="footer-contact__icon" aria-hidden="true">✉</span>
            Contatti: <a href="mailto:infoflightracker@gmail.com">infoflightracker@gmail.com</a>
        </p>
        <nav class="footer-links" aria-label="Link footer">
            <a href="{{ route('about') }}">Chi siamo</a>
            <span class="footer-links__sep" aria-hidden="true">·</span>
            <a href="{{ route('privacy') }}">Privacy</a>
            <span class="footer-links__sep" aria-hidden="true">·</span>
            <a href="{{ route('terms') }}">Termini di utilizzo</a>
        </nav>
        <p class="footer-copy">&copy; 2025 FlightTracker – Tutti i diritti riservati</p>
    </div>
</footer>
