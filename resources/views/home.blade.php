<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<body>
    <nav class="navbar navbar-light bg-light px-4">
        <div class="container-fluid d-flex justify-content-between">

            <ul class="navbar-nav d-flex flex-row">
                <li class="nav-item me-3">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Altro</a>
                </li>
            </ul>

            <ul class="navbar-nav d-flex flex-row">
                @auth
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#">Area personale</a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-decoration-none">Esci</button>
                        </form>
                    </li>
                @endauth

                @guest
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('login.form') }}">Accedi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.form') }}">Registrati</a>
                    </li>
                @endguest
            </ul>


        </div>
    </nav>

    <div class="search-section">

        <h1 class="search-title">Cerca un volo in tempo reale</h1>

        <form action="#" method="GET" class="search-form">
            <div class="input-group">
                <span class="input-group-text search-icon">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control search-input" placeholder="Inserisci codice volo" name="query">
            </div>
        </form>

    </div>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <p class="mb-1">📧 Contatti: <a href="mailto:info@flighttracker.it">info@flighttracker.it</a></p>
                <p class="mb-1">Privacy · Termini di utilizzo</p>
                <p class="mt-3 text-muted">&copy; 2025 FlightTracker – Tutti i diritti riservati</p>
            </div>
        </div>
    </footer>



</body>
</html>
