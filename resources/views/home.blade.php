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
                @if(auth()->user()->is_admin)
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Area personale</a>
                    </li>
                @else
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('user.profile') }}">Area personale</a>
                    </li>
                @endif
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

<!-- Contenitore principale -->
<div class="main-content">
    <div class="search-section" id="search-section">
        <h1 class="search-title" id="search-title">Cerca un volo in tempo reale</h1>

        <form action="#" method="GET" class="search-form">
            <div class="input-group">
                <span class="input-group-text search-icon">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control search-input" id="search-input" placeholder="Inserisci volo" name="query" autocomplete="off">
            </div>
        </form>
    </div>

    <!-- Risultati AJAX -->
    <div id="result-container" class="container mt-5"></div>
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

<script>
    const input = document.getElementById("search-input");
    const title = document.getElementById("search-title");
    const searchSection = document.getElementById("search-section");
    const resultContainer = document.getElementById("result-container");

    const words = ["volo", "aeroporto", "città"];
    let index = 0;
    let placeholderInterval = setInterval(updatePlaceholder, 3000);

    function updatePlaceholder() {
        index = (index + 1) % words.length;
        input.setAttribute("placeholder", "Inserisci " + words[index]);
    }

    input.addEventListener("input", function () {
        const query = this.value.trim();

        if (query.length > 0 && placeholderInterval !== null) {
            clearInterval(placeholderInterval);
            placeholderInterval = null;
        }

        if (query.length === 0 && placeholderInterval === null) {
            placeholderInterval = setInterval(updatePlaceholder, 3000);
        }

        if (query.length >= 1) {
            fetch(`/search-flights?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    title.style.display = "none";
                    searchSection.classList.add("search-fixed");
                    resultContainer.innerHTML = "";

                    if (data.length === 0) {
                        resultContainer.innerHTML = '<p class="text-center text-muted">Nessun volo trovato.</p>';
                        return;
                    }

                    data.forEach(flight => {
                        const card = document.createElement("div");
                        card.className = "flight-card";
                        card.style.cursor = "pointer"; // mano quando vai sopra

                        card.addEventListener("click", () => {
                            window.location.href = `/flights/${flight.id}`;
                        });

                        const departureCity = flight.departure_airport.city;
                        const arrivalCity = flight.arrival_airport.city;
                        const departureTime = new Date(flight.departure_time).toLocaleString();
                        const arrivalTime = new Date(flight.arrival_time).toLocaleString();
                        const planeImage = flight.airplane_model.image_path;

                        card.innerHTML = `
                            <img src="${planeImage}" alt="Aereo" class="flight-image">
                            <div>
                                <h5>${departureCity} → ${arrivalCity}</h5>
                                <p>${departureTime} - ${arrivalTime}</p>
                                <small>Modello: ${flight.airplane_model.name}</small>
                            </div>
                        `;
                        resultContainer.appendChild(card);
                    });

                });
        } else {
            title.style.display = "block";
            searchSection.classList.remove("search-fixed");
            resultContainer.innerHTML = "";
        }
    });
</script>
</body>
</html>
