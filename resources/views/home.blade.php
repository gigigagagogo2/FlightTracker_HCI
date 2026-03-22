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
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
@include('navbar')

<div class="main-content">

    <!-- ── HERO ── -->
    <div class="hero-section" id="search-section">
        <div class="hero-bg-lines" aria-hidden="true">
            <svg width="100%" height="100%" viewBox="0 0 1200 500" preserveAspectRatio="xMidYMid slice">
                <path d="M0 200 Q300 140 600 200 Q900 260 1200 180" stroke="#f59e0b" stroke-width="1" fill="none" opacity="0.08"/>
                <path d="M0 320 Q400 260 800 320 Q1000 350 1200 290" stroke="#22d3ee" stroke-width="1" fill="none" opacity="0.06"/>
                <path d="M0 400 Q350 370 700 410 Q950 440 1200 380" stroke="#f59e0b" stroke-width="0.5" fill="none" opacity="0.05"/>
                <circle cx="220" cy="180" r="3" fill="#f59e0b" opacity="0.3"/>
                <circle cx="580" cy="215" r="3" fill="#22d3ee" opacity="0.3"/>
                <circle cx="920" cy="175" r="3" fill="#f59e0b" opacity="0.3"/>
                <line x1="220" y1="180" x2="580" y2="215" stroke="#f59e0b" stroke-width="0.8" stroke-dasharray="8,6" opacity="0.15"/>
                <line x1="580" y1="215" x2="920" y2="175" stroke="#f59e0b" stroke-width="0.8" stroke-dasharray="8,6" opacity="0.15"/>
            </svg>
        </div>

        <div class="hero-content">
            <div class="hero-badge" id="search-title">
                <span class="pulse-dot"></span>
                <span>Monitoraggio in tempo reale</span>
            </div>
            <h1 class="hero-title" id="hero-title-text">
                Traccia ogni <span class="hero-accent">volo</span> nel mondo
            </h1>
            <p class="hero-subtitle" id="hero-subtitle">Monitoraggio live, coordinate, velocità e rotta. Sempre aggiornato.</p>

            <!-- Search -->
            <form action="#" method="GET" class="search-form" id="search-form">
                <div class="search-container">
                    <i class="bi bi-search search-icon-inner"></i>
                    <input type="text" class="search-input" id="search-input"
                           placeholder="Cerca aeroporto, città o volo..."
                           name="query" autocomplete="off">
                </div>
            </form>

            <!-- Filtri -->
            <div class="filter-bar" id="filter-bar">
                <button class="filter-pill" id="btn-in-arrivo">
                    <i class="bi bi-airplane-engines-fill"></i> In arrivo
                </button>
                <button class="filter-pill" id="btn-in-partenza">
                    <i class="bi bi-airplane-fill"></i> In partenza
                </button>
                <button class="filter-pill" id="btn-atterrati">
                    <i class="bi bi-geo-alt-fill"></i> Atterrati
                </button>
                <button class="filter-pill filter-pill--country" id="btn-paese">
                    <span id="paese-flag"></span>
                    <span id="paese-label">Rilevamento...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ── STATS BAR ── -->
    <div class="stats-bar" id="stats-bar">
        <div class="stat-item">
            <span class="stat-value stat-value--amber">{{ $popolari->count() + $vicino->count() }}</span>
            <span class="stat-label">Voli attivi</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-value stat-value--cyan">{{ \App\Models\Airport::count() }}</span>
            <span class="stat-label">Aeroporti</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-value">{{ \App\Models\Airport::distinct('country')->count('country') }}</span>
            <span class="stat-label">Paesi</span>
        </div>
    </div>

    <!-- ── CARD SEZIONI STATICHE ── -->
    <div id="cards-container">

        <!-- Voli Popolari -->
        <section class="flights-section">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">In evidenza</p>
                    <h2 class="section-title">Voli popolari</h2>
                </div>
            </div>
            <div class="carousel-wrapper">
                <button class="carousel-btn carousel-btn--prev" id="carousel-prev">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="flights-grid" id="grid-popolari">
                    @foreach($popolari as $flight)
                        <a href="/flights/{{ $flight->id }}" class="flight-card-new">
                            <div class="flight-card-map">
                                <div class="flight-card-photos">
                                    <div class="photo-left"
                                         style="background-image: url('/{{ $flight->departureAirport->image_path ?? 'images/airport-placeholder.jpg' }}')">
                                    </div>
                                    <div class="photo-right"
                                         style="background-image: url('/{{ $flight->arrivalAirport->image_path ?? 'images/airport-placeholder.jpg' }}')">
                                    </div>
                                    <div class="photo-diagonal"></div>
                                </div>
                                <div class="flight-card-badge flight-card-badge--live">
                                    <span class="badge-dot"></span> In volo
                                </div>
                            </div>
                            <div class="flight-card-body">
                                <div class="flight-card-route">
                                    {{ $flight->departureAirport->name }}
                                    <i class="bi bi-arrow-right"></i>
                                    {{ $flight->arrivalAirport->name }}
                                </div>
                                <div class="flight-card-meta">
                                    <span class="flight-card-model">{{ $flight->airplaneModel->name }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="carousel-btn carousel-btn--next" id="carousel-next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- Voli Vicino a Te -->
        <section class="flights-section">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Basato sulla tua posizione</p>
                    <h2 class="section-title">Voli vicino a te</h2>
                </div>
            </div>
            <p id="vicino-no-results" class="no-results" style="display:none;">
                Nessun volo disponibile nel tuo paese al momento.
            </p>
            <div class="carousel-wrapper">
                <button class="carousel-btn carousel-btn--prev" id="carousel-vicino-prev">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="flights-grid" id="grid-vicino">
                    @foreach($vicino as $flight)
                        <a href="/flights/{{ $flight->id }}" class="flight-card-new"
                           data-dep-country="{{ $flight->departureAirport->country }}"
                           data-arr-country="{{ $flight->arrivalAirport->country }}">
                            <div class="flight-card-map">
                                <div class="flight-card-photos">
                                    <div class="photo-left"
                                         style="background-image: url('/{{ $flight->departureAirport->image_path ?? 'images/airport-placeholder.jpg' }}')">
                                    </div>
                                    <div class="photo-right"
                                         style="background-image: url('/{{ $flight->arrivalAirport->image_path ?? 'images/airport-placeholder.jpg' }}')">
                                    </div>
                                    <div class="photo-diagonal"></div>
                                </div>
                                <div class="flight-card-badge flight-card-badge--live">
                                    <span class="badge-dot"></span> In volo
                                </div>
                            </div>
                            <div class="flight-card-body">
                                <div class="flight-card-route">
                                    {{ $flight->departureAirport->name }}
                                    <i class="bi bi-arrow-right"></i>
                                    {{ $flight->arrivalAirport->name }}
                                </div>
                                <div class="flight-card-meta">
                                    <span class="flight-card-model">{{ $flight->airplaneModel->name }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="carousel-btn carousel-btn--next" id="carousel-vicino-next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </section>

    </div>

    <!-- ── RISULTATI RICERCA ── -->
    <div class="container-search" id="result-container" style="display: none;"></div>

</div>

@include('footer')

<script>
    // ── IP GEOLOCATION ──
    let vinoCards = []; // riferimento globale

    // ── IP GEOLOCATION ──
    (async () => {
        try {
            const res = await fetch('https://ipapi.co/json/');

            const data = await res.json();

            const codice = data.country_code || '';

            // Converti il codice ISO in nome italiano tramite API nativa del browser
            let paese = '';
            if (codice) {
                try {
                    const displayNames = new Intl.DisplayNames(['it'], { type: 'region' });
                    paese = displayNames.of(codice) || data.country_name || '';
                } catch(e) {
                    paese = data.country_name || '';
                }
            }

            const flag = codice
                ? [...codice.toUpperCase()].map(c =>
                    String.fromCodePoint(0x1F1E6 - 65 + c.charCodeAt(0))
                ).join('')
                : '';

            document.getElementById('paese-flag').textContent = flag;
            document.getElementById('paese-label').textContent = paese || 'Paese';
            document.getElementById('btn-paese').dataset.country = paese;

            filterVicinoPaese(paese);

            filtri.inPaese = volo => {
                const c1 = (volo.departure_airport?.country || '').toLowerCase();
                const c2 = (volo.arrival_airport?.country || '').toLowerCase();
                const target = paese.toLowerCase();
                return c1.includes(target) || c2.includes(target);
            };
        } catch(e) {
            document.getElementById('paese-label').textContent = 'Paese';
        }
    })();

    function filterVicinoPaese(paese) {
        const grid = document.getElementById('grid-vicino');
        const noResults = document.getElementById('vicino-no-results');
        const carouselWrapper = document.querySelector('#grid-vicino').closest('.carousel-wrapper');
        const allCards = Array.from(grid.querySelectorAll('.flight-card-new'));

        if (!paese) return;

        const target = paese.toLowerCase();
        const filtered = allCards.filter(card => {
            const dep = (card.dataset.depCountry || '').toLowerCase();
            const arr = (card.dataset.arrCountry || '').toLowerCase();
            return dep.includes(target) || arr.includes(target);
        });

        allCards.forEach(c => c.style.display = 'none');

        if (filtered.length === 0) {
            carouselWrapper.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            carouselWrapper.style.display = 'flex';
            noResults.style.display = 'none';

            // Se meno di 3 card centra la griglia
            if (filtered.length < 3) {
                grid.style.gridTemplateColumns = `repeat(${filtered.length}, minmax(0, 280px))`;
                grid.style.justifyContent = 'center';
            } else {
                grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                grid.style.justifyContent = '';
            }

            initCarouselCards(filtered, 'carousel-vicino-prev', 'carousel-vicino-next');
        }
    }

    function initCarouselCards(cards, prevId, nextId) {
        const btnPrev = document.getElementById(prevId);
        const btnNext = document.getElementById(nextId);
        if (!btnPrev || !btnNext || cards.length === 0) return;

        const perPage = 3;
        let currentPage = 0;
        const totalPages = Math.ceil(cards.length / perPage);

        function renderPage(page) {
            cards.forEach((card, i) => {
                card.style.display = (i >= page * perPage && i < (page + 1) * perPage)
                    ? 'block' : 'none';
            });
            btnPrev.disabled = page === 0;
            btnNext.disabled = page >= totalPages - 1;
        }

        btnPrev.addEventListener('click', () => { if (currentPage > 0) renderPage(--currentPage); });
        btnNext.addEventListener('click', () => { if (currentPage < totalPages - 1) renderPage(++currentPage); });

        renderPage(0);
    }

    function initCarousel(gridId, prevId, nextId) {
        const grid = document.getElementById(gridId);
        if (!grid) return;
        const cards = Array.from(grid.querySelectorAll('.flight-card-new'));
        initCarouselCards(cards, prevId, nextId);
    }

    initCarousel('grid-popolari', 'carousel-prev', 'carousel-next');
    // grid-vicino viene inizializzato dopo il filtro paese

    // ── SEARCH LOGIC ──
    const input = document.getElementById("search-input");
    const resultContainer = document.getElementById("result-container");
    const cardsContainer = document.getElementById("cards-container");
    const statsBar = document.getElementById("stats-bar");
    const heroTitleText = document.getElementById("hero-title-text");
    const heroSubtitle = document.getElementById("hero-subtitle");
    const heroBadge = document.querySelector(".hero-badge");

    const words = ["aeroporto", "città", "volo"];
    let index = 0;
    setInterval(() => {
        index = (index + 1) % words.length;
        input.setAttribute("placeholder", "Cerca " + words[index] + "...");
    }, 3000);

    function disableSearch() {
        cardsContainer.style.display = "block";
        statsBar.style.display = "flex";
        resultContainer.style.display = "none";
        resultContainer.innerHTML = "";
        heroTitleText.style.display = "block";
        heroSubtitle.style.display = "block";
        heroBadge.style.display = "inline-flex";
        document.getElementById("search-section").classList.remove("search-active");
    }

    function deselectAll() {
        document.querySelectorAll(".filter-pill").forEach(b => b.classList.remove("selected"));
    }

    function showResults(data) {
        document.getElementById("search-section").classList.add("search-active");
        heroTitleText.style.display = "none";
        heroSubtitle.style.display = "none";
        heroBadge.style.display = "none";
        cardsContainer.style.display = "none";
        statsBar.style.display = "none";
        resultContainer.style.display = "block";

        let filteredData = [...data];
        const activeBtn = document.querySelector(".filter-pill.selected");
        const id = activeBtn?.id;

        const filtro = {
            "btn-in-arrivo": filtri.inArrivoProssimi,
            "btn-in-partenza": filtri.inPartenzaProssimi,
            "btn-atterrati": filtri.atterrati,
            "btn-paese": filtri.inPaese
        }[id];

        if (filtro) {
            filteredData = filteredData.filter(filtro);
        } else {
            filteredData = filteredData.filter(f => f.status !== "red");
        }

        if (filteredData.length === 0) {
            resultContainer.innerHTML = '<p class="no-results">Nessun volo trovato.</p>';
            return;
        }

        resultContainer.innerHTML = '<h3 class="results-title">Risultati</h3>';

        filteredData.forEach(flight => {
            const statusClass = ['green','yellow','red'].includes(flight.status) ? flight.status : 'gray';
            const card = document.createElement("div");
            card.className = "flight-card";
            card.addEventListener("click", () => window.location.href = `/flights/${flight.id}`);
            card.innerHTML = `
                <div class="flight-card-content">
                    <span class="status-dot ${statusClass}"></span>
                    <img src="${flight.airplane_model.image_path}" alt="Aereo" class="airplane-image">
                    <div class="flight-info">
                        <h5 class="flight-route">
                            ${flight.departure_airport.name}
                            <span class="route-arrow"><i class="bi bi-airplane-fill"></i></span>
                            ${flight.arrival_airport.name}
                        </h5>
                        <p class="flight-times">
                            ${new Date(flight.departure_time).toLocaleString('it-IT',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})}
                            <span class="arrow">→</span>
                            ${new Date(flight.arrival_time).toLocaleString('it-IT',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})}
                        </p>
                        <p class="aircraft-info"><strong>${flight.airplane_model.name}</strong></p>
                    </div>
                </div>`;
            resultContainer.appendChild(card);
        });
    }

    input.addEventListener("input", () => {
        const query = input.value.trim();
        const filtroAttivo = document.querySelector(".filter-pill.selected");
        if (query.length > 0 || filtroAttivo) {
            fetch(`/search-flights?query=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => showResults(data));
        } else {
            disableSearch();
        }
    });

    document.querySelectorAll(".filter-bar .filter-pill").forEach(btn => {
        btn.addEventListener("click", () => {
            const wasSelected = btn.classList.contains("selected");
            deselectAll();
            if (!wasSelected) btn.classList.add("selected");
            const query = input.value.trim();
            const filtroAttivo = document.querySelector(".filter-pill.selected");
            if (!query && !filtroAttivo) { disableSearch(); return; }
            fetch(`/search-flights?query=${encodeURIComponent(query)}`)
                .then(r => r.json())
                .then(data => showResults(data));
        });
    });

    const filtri = {
        inArrivoProssimi: v => v.departure_time <= Date.now && v.arrival_time && Date.now() <= new Date(v.arrival_time).getTime() && new Date(v.arrival_time).getTime() <= Date.now() + 2*3600000,
        inPartenzaProssimi: v => v.departure_time && Date.now() <= new Date(v.departure_time).getTime() && new Date(v.departure_time).getTime() <= Date.now() + 2*3600000,
        atterrati: v => v.status === "red",
        inPaese: () => false,
    };

    // ── CAROUSEL VOLI POPOLARI ──
    function initCarousel(gridId, prevId, nextId) {
        const grid = document.getElementById(gridId);
        const btnPrev = document.getElementById(prevId);
        const btnNext = document.getElementById(nextId);
        if (!grid || !btnPrev || !btnNext) return;

        const cards = Array.from(grid.querySelectorAll('.flight-card-new'));
        const perPage = 3;
        let currentPage = 0;
        const totalPages = Math.ceil(cards.length / perPage);

        function renderPage(page) {
            cards.forEach((card, i) => {
                card.style.display = (i >= page * perPage && i < (page + 1) * perPage)
                    ? 'block' : 'none';
            });
            btnPrev.disabled = page === 0;
            btnNext.disabled = page >= totalPages - 1;
        }

        btnPrev.addEventListener('click', () => { if (currentPage > 0) renderPage(--currentPage); });
        btnNext.addEventListener('click', () => { if (currentPage < totalPages - 1) renderPage(++currentPage); });

        renderPage(0);
    }

    initCarousel('grid-popolari', 'carousel-prev', 'carousel-next');
    initCarousel('grid-vicino', 'carousel-vicino-prev', 'carousel-vicino-next');

</script>
</body>
</html>
