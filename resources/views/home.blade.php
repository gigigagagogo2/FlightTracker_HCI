<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlightTracker</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
@include('navbar')

<!-- Contenitore principale -->
<div class="main-content">
    <div class="search-section" id="search-section">
        <h1 class="search-title" id="search-title">Cerca un volo in tempo reale</h1>

        <form action="#" method="GET" class="search-form">
            <div class="search-container">
                <div class="input-group">
                    <span class="input-group-text search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control search-input" id="search-input" placeholder="Inserisci volo"
                           name="query" autocomplete="off">
                </div>
            </div>
        </form>

        <div class="button-bar d-flex justify-content-center gap-2 flex-wrap mt-3">
            <button class="button secondary" id="btn-in-arrivo">
                <i class="bi bi-airplane-engines-fill me-1"></i> In arrivo
            </button>
            <button class="button secondary" id="btn-in-partenza">
                <i class="bi bi-airplane-fill me-1 rotate-180"></i> In partenza
            </button>
            <button class="button secondary" id="btn-atterrati">
                <i class="bi bi-geo-alt-fill me-1"></i> Atterrati
            </button>
            <button class="button secondary" id="btn-italia">
                <i class="bi bi-flag-fill me-1"></i> Italia
            </button>
        </div>
    </div>

    <!-- Contenitore per le card statiche iniziali -->
    <div class="row mt-5 px-3" id="cards-container">
        <!-- VOLI POPOLARI -->
        <div class="mb-5 text-center">
            <h3 class="mb-4">Voli Popolari</h3>
            <div id="carouselPopolari" class="carousel slide mx-auto" data-bs-ride="carousel" style="max-width: 900px;">
                <div class="carousel-inner">
                    @foreach($popolari->chunk(2) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="d-flex justify-content-center gap-4">
                                @foreach($chunk as $flight)
                                    <div class="card shadow-sm" style="width: 22rem; cursor: pointer;" onclick="window.location.href='/flights/{{ $flight->id }}'">
                                        <img src="/images/city/city{{ rand(1,18) }}.jpg" class="card-img-top fixed-img"
                                             alt="{{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $flight->departureAirport->city }}
                                                → {{ $flight->arrivalAirport->city }}</h5>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselPopolari"
                        data-bs-slide="prev">
                    <i class="bi bi-caret-left-fill fs-2"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselPopolari"
                        data-bs-slide="next">
                    <i class="bi bi-caret-right-fill fs-2"></i>
                </button>
            </div>
        </div>

        <!-- VOLI VICINO A TE -->
        <div class="mb-5 text-center">
            <h3 class="mb-4">Voli Vicino a Te</h3>
            <div id="carouselVicino" class="carousel slide mx-auto" data-bs-ride="carousel" style="max-width: 900px;">
                <div class="carousel-inner">
                    @foreach($vicino->chunk(2) as $index => $chunk)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="d-flex justify-content-center gap-4">
                                @foreach($chunk as $flight)
                                    <div class="card shadow-sm" style="width: 22rem; cursor: pointer;" onclick="window.location.href='/flights/{{ $flight->id }}'">
                                        <img src="/images/city/city{{ rand(1,18) }}.jpg" class="card-img-top fixed-img"
                                             alt="{{ $flight->departureAirport->city }} → {{ $flight->arrivalAirport->city }}">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $flight->departureAirport->city }}
                                                → {{ $flight->arrivalAirport->city }}</h5>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselVicino"
                        data-bs-slide="prev">
                    <i class="bi bi-caret-left-fill fs-2"></i>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselVicino"
                        data-bs-slide="next">
                    <i class="bi bi-caret-right-fill fs-2"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="container mt-5" id="result-container" style="display: none;">
        <!-- Card ricerca dinamica -->
    </div>
</div>

@include('footer')

<script>
    const input = document.getElementById("search-input");
    const searchTitle = document.getElementById("search-title");
    const resultContainer = document.getElementById("result-container");
    const cardsContainer = document.getElementById("cards-container");
    const searchSection = document.getElementById("search-section");
    let isSearchActive = false;

    let currentFlights = [];

    const words = ["volo", "aeroporto", "città"];
    let index = 0;
    setInterval(() => {
        index = (index + 1) % words.length;
        input.setAttribute("placeholder", "Inserisci " + words[index]);
    }, 3000);

    function disableSearch() {
        isSearchActive = false;
        searchSection.classList.remove("search-fixed");
        searchTitle.style.display = "block";
        cardsContainer.style.display = "block";
        resultContainer.style.display = "none";
        resultContainer.innerHTML = "";
    }

    function deselectAll() {
        document.querySelectorAll(".button").forEach(b => b.classList.remove("selected"));
    }

    function isFilterActive() {
        return document.querySelector(".button.selected") !== null;
    }

    function showResults(data) {
        searchSection.classList.add("search-fixed");
        currentFlights = data;
        searchTitle.style.display = "none";
        resultContainer.innerHTML = `
        <h3 class="mb-4 text-left" id="search-results-title">Risultati della ricerca</h3>
        `

        const titleNode = document.getElementById("search-results-title");
        if (titleNode) resultContainer.appendChild(titleNode);

        let filteredData = [...data];
        const activeBtn = document.querySelector(".button.selected");

        const id = activeBtn?.id;
        const filtro = {
            "btn-in-arrivo": filtri.inArrivoProssimi,
            "btn-in-partenza": filtri.inPartenzaProssimi,
            "btn-atterrati": filtri.atterrati,
            "btn-italia": filtri.inItalia
        }[id];

        if (filtro) {
            filteredData = filteredData.filter(filtro);
        } else {
            filteredData = filteredData.filter(f => f.status !== "red");
        }

        if (filteredData.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">Nessun volo trovato.</p>';
            return;
        }

        filteredData.forEach(flight => {
            const statusClass = ['green', 'yellow', 'red'].includes(flight.status) ? flight.status : 'gray';
            const card = document.createElement("div");
            card.className = "flight-card";
            card.addEventListener("click", () => window.location.href = `/flights/${flight.id}`);

            card.innerHTML = `
    <div class="flight-card-content">
        <span class="status-dot ${statusClass}"></span>

        <img src="${flight.airplane_model.image_path}" alt="Aereo" class="airplane-image">

        <div class="flight-info">
            <h5 class="flight-route">
                ${flight.departure_airport.city}
                <span class="route-arrow">✈</span>
                ${flight.arrival_airport.city}
            </h5>

            <p class="flight-times">
                ${new Date(flight.departure_time).toLocaleString('it-IT', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}
                <span class="arrow">→</span>
                ${new Date(flight.arrival_time).toLocaleString('it-IT', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })}
            </p>

            <p class="aircraft-info">
                <span class="aircraft-emoji">✈️</span>
                <strong class="aircraft-name">${flight.airplane_model.name}</strong>
            </p>
        </div>
    </div>
`;

            resultContainer.appendChild(card);
        });
    }

    input.addEventListener("input", () => {
        const query = input.value.trim();
        const filtroAttivo = document.querySelector(".button.selected");

        if (query.length > 0 || filtroAttivo) {
            fetch(`/search-flights?query=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => {
                    searchTitle.style.display = "block";
                    cardsContainer.style.display = "none";
                    resultContainer.style.display = "block";
                    showResults(data);
                });
        } else {
            disableSearch();
        }
    });

    document.querySelectorAll(".button-bar .button").forEach(btn => {
        btn.addEventListener("click", () => {
            const wasSelected = btn.classList.contains("selected");
            deselectAll();
            if (!wasSelected) {
                btn.classList.add("selected");
            }
            const query = input.value.trim();
            const filtroAttivo = document.querySelector(".button.selected");
            if (!query && !filtroAttivo) {
                disableSearch();
                return;
            }
            isSearchActive = true;
            const url = `/search-flights?query=${encodeURIComponent(query)}`;
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    cardsContainer.style.display = "none";
                    resultContainer.style.display = "block";
                    showResults(data);
                })
                .catch(err => console.error(err));
        });
    });

    const filtri = {
        inArrivoProssimi: volo => volo.departure_time <= Date.now && volo.arrival_time && Date.now() <= new Date(volo.arrival_time).getTime() && new Date(volo.arrival_time).getTime() <= Date.now() + 2 * 3600000,
        inPartenzaProssimi: volo => volo.departure_time && Date.now() <= new Date(volo.departure_time).getTime() && new Date(volo.departure_time).getTime() <= Date.now() + 2 * 3600000,
        atterrati: volo => volo.status === "red",
        inItalia: volo => {
            const c1 = (volo.departure_airport.country || "").toLowerCase();
            const c2 = (volo.arrival_airport.country || "").toLowerCase();
            return ["italia", "italy", "it"].includes(c1) || ["italia", "italy", "it"].includes(c2);
        }
    };
</script>

</body>
</html>
