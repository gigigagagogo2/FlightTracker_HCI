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

            <form action="#" method="GET" class="search-form" id="search-form">
                <div class="search-container">
                    <i class="bi bi-search search-icon-inner"></i>
                    <input type="text" class="search-input" id="search-input"
                           placeholder="Cerca aeroporto o città..."
                           name="query" autocomplete="off">
                </div>
            </form>

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
            <span class="stat-value stat-value--amber" id="stat-voli-attivi">{{ $popolari->count() }}</span>
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
            <p id="popolari-no-results" class="no-results" style="display:none;">
                Nessun volo popolare disponibile al momento.
            </p>
            <div class="carousel-wrapper" id="carousel-wrapper-popolari">
                <button class="carousel-btn" id="carousel-prev">
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
                                    <i class="bi bi-arrow-right flex-shrink-0"></i>
                                    {{ $flight->arrivalAirport->name }}
                                </div>
                                <div class="flight-card-meta">
                                    <span class="flight-card-model">{{ $flight->airplaneModel->name }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button class="carousel-btn" id="carousel-next">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </section>

        <!-- Voli Vicino a Te — popolati dinamicamente via JS -->
        <section class="flights-section" id="section-vicino">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Basato sulla tua posizione</p>
                    <h2 class="section-title">Voli vicino a te</h2>
                </div>
            </div>

            <!-- Stato di caricamento -->
            <div id="vicino-loading" class="vicino-loading">
                <span class="pulse-dot" style="background:var(--cyan);"></span>
                <span>Rilevamento posizione...</span>
            </div>

            <!-- Messaggio nessun volo -->
            <p id="vicino-no-results" class="no-results" style="display:none;">
                Nessun volo disponibile nel tuo paese al momento.
            </p>

            <!-- Carousel (nascosto finché non arrivano i dati) -->
            <div class="carousel-wrapper" id="carousel-wrapper-vicino" style="display:none;">
                <button class="carousel-btn" id="carousel-vicino-prev">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="flights-grid" id="grid-vicino"></div>
                <button class="carousel-btn" id="carousel-vicino-next">
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
    // ── HELPERS ──
    function getFlag(countryCode) {
        if (!countryCode || countryCode.length !== 2) return '';
        return [...countryCode.toUpperCase()]
            .map(c => String.fromCodePoint(0x1F1E6 + c.charCodeAt(0) - 65))
            .join('');
    }

    function buildFlightCard(flight) {
        const depImg = flight.departure_airport.image_path
            ? '/' + flight.departure_airport.image_path
            : '/images/airport-placeholder.jpg';
        const arrImg = flight.arrival_airport.image_path
            ? '/' + flight.arrival_airport.image_path
            : '/images/airport-placeholder.jpg';

        return `
        <a href="/flights/${flight.id}" class="flight-card-new">
            <div class="flight-card-map">
                <div class="flight-card-photos">
                    <div class="photo-left" style="background-image: url('${depImg}')"></div>
                    <div class="photo-right" style="background-image: url('${arrImg}')"></div>
                    <div class="photo-diagonal"></div>
                </div>
                <div class="flight-card-badge flight-card-badge--live">
                    <span class="badge-dot"></span> In volo
                </div>
            </div>
            <div class="flight-card-body">
                <div class="flight-card-route">
                    ${flight.departure_airport.name}
                    <i class="bi bi-arrow-right flex-shrink-0"></i>
                    ${flight.arrival_airport.name}
                </div>
                <div class="flight-card-meta">
                    <span class="flight-card-model">${flight.airplane_model.name}</span>
                </div>
            </div>
        </a>`;
    }

    // ── CAROUSEL ──
    function initCarouselCards(cards, prevId, nextId, gridId) {
        const btnPrev = document.getElementById(prevId);
        const btnNext = document.getElementById(nextId);
        const grid = gridId ? document.getElementById(gridId) : null;
        if (!btnPrev || !btnNext || cards.length === 0) return;

        const perPage = 3;
        let currentPage = 0;
        const totalPages = Math.ceil(cards.length / perPage);

        function renderPage(page) {
            const visible = cards.slice(page * perPage, (page + 1) * perPage);
            cards.forEach(c => c.style.display = 'none');
            visible.forEach(c => c.style.display = 'block');

            if (grid) {
                if (visible.length < 3) {
                    grid.style.gridTemplateColumns = `repeat(${visible.length}, minmax(0, 280px))`;
                    grid.style.justifyContent = 'center';
                } else {
                    grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                    grid.style.justifyContent = '';
                }
            }

            document.getElementById(prevId).disabled = page === 0;
            document.getElementById(nextId).disabled = page >= totalPages - 1;
        }

        const newPrev = btnPrev.cloneNode(true);
        const newNext = btnNext.cloneNode(true);
        btnPrev.parentNode.replaceChild(newPrev, btnPrev);
        btnNext.parentNode.replaceChild(newNext, btnNext);

        newPrev.addEventListener('click', () => { if (currentPage > 0) { currentPage--; renderPage(currentPage); } });
        newNext.addEventListener('click', () => { if (currentPage < totalPages - 1) { currentPage++; renderPage(currentPage); } });

        renderPage(0);
    }

    function initCarousel(gridId, prevId, nextId, noResultsId, wrapperId) {
        const grid = document.getElementById(gridId);
        const noResults = noResultsId ? document.getElementById(noResultsId) : null;
        const wrapper = wrapperId ? document.getElementById(wrapperId) : null;
        if (!grid) return;

        const cards = Array.from(grid.querySelectorAll('.flight-card-new'));

        if (cards.length === 0) {
            if (wrapper) wrapper.style.display = 'none';
            if (noResults) noResults.style.display = 'block';
            return;
        }

        initCarouselCards(cards, prevId, nextId, gridId);
    }

    initCarousel('grid-popolari', 'carousel-prev', 'carousel-next', 'popolari-no-results', 'carousel-wrapper-popolari');

    // ── FILTRI ──
    const filtri = {
        inArrivoProssimi: v => v.arrival_time && Date.now() <= new Date(v.arrival_time).getTime() && new Date(v.arrival_time).getTime() <= Date.now() + 2*3600000,
        inPartenzaProssimi: v => v.departure_time && Date.now() <= new Date(v.departure_time).getTime() && new Date(v.departure_time).getTime() <= Date.now() + 2*3600000,
        atterrati: v => v.status === "red",
        inPaese: () => false,
    };

    // ── IP GEOLOCATION + CARICAMENTO VOLI VICINO ──
    (async () => {
        const loading = document.getElementById('vicino-loading');
        const noResults = document.getElementById('vicino-no-results');
        const wrapper = document.getElementById('carousel-wrapper-vicino');
        const grid = document.getElementById('grid-vicino');

        try {
            const res = await fetch('https://ipapi.co/json/?token={{ env('IPAPI_API') }}');
            const data = await res.json();
            const codice = data.country_code || '';

            if (!codice) {
                loading.style.display = 'none';
                document.getElementById('paese-flag').textContent = '⚠️';
                document.getElementById('paese-label').textContent = 'Posizione non rilevata';
                document.getElementById('btn-paese').title = 'Non è stato possibile rilevare il paese di connessione';
                noResults.style.display = 'block';
                return;
            }

            let paese = '';
            try {
                paese = new Intl.DisplayNames(['it'], { type: 'region' }).of(codice) || data.country_name || '';
            } catch(e) {
                paese = data.country_name || '';
            }

            document.getElementById('paese-flag').textContent = getFlag(codice);
            document.getElementById('paese-label').textContent = paese || 'Paese';
            document.getElementById('btn-paese').dataset.country = paese;

            filtri.inPaese = volo => {
                const c1 = (volo.departure_airport?.country || '').toLowerCase();
                const c2 = (volo.arrival_airport?.country || '').toLowerCase();
                return c1.includes(paese.toLowerCase()) || c2.includes(paese.toLowerCase());
            };

            // Carica voli vicino a te tramite endpoint dedicato
            const vinoRes = await fetch(`/flights/vicino?paese=${encodeURIComponent(paese)}`);
            const flights = await vinoRes.json();

            loading.style.display = 'none';

            if (!flights || flights.length === 0) {
                noResults.style.display = 'block';
                return;
            }

            // Popola la griglia con le card
            grid.innerHTML = flights.map(f => buildFlightCard(f)).join('');

            wrapper.style.display = 'flex';

            const cards = Array.from(grid.querySelectorAll('.flight-card-new'));
            initCarouselCards(cards, 'carousel-vicino-prev', 'carousel-vicino-next', 'grid-vicino');

        } catch(e) {
            loading.style.display = 'none';
            document.getElementById('paese-flag').textContent = '⚠️';
            document.getElementById('paese-label').textContent = 'Posizione non rilevata';
            document.getElementById('btn-paese').title = 'Non è stato possibile rilevare il paese di connessione';
            noResults.style.display = 'block';
        }
    })();

    // ── SEARCH LOGIC ──
    const input = document.getElementById("search-input");
    const resultContainer = document.getElementById("result-container");
    const cardsContainer = document.getElementById("cards-container");
    const statsBar = document.getElementById("stats-bar");
    const heroTitleText = document.getElementById("hero-title-text");
    const heroSubtitle = document.getElementById("hero-subtitle");
    const heroBadge = document.querySelector(".hero-badge");

    const words = ["aeroporto", "città" ];
    let wordIndex = 0;
    setInterval(() => {
        wordIndex = (wordIndex + 1) % words.length;
        input.setAttribute("placeholder", "Cerca " + words[wordIndex] + "...");
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

    const statusConfig = {
        green:  { label: 'In volo',     borderColor: 'rgba(34,211,238,0.3)',  color: '#22d3ee', dotAnim: 'pulse-anim' },
        yellow: { label: 'In partenza', borderColor: 'rgba(245,158,11,0.3)',  color: '#f59e0b', dotAnim: 'pulse-anim' },
        red:    { label: 'Atterrato',   borderColor: 'rgba(239,68,68,0.3)',   color: '#ef4444', dotAnim: '' },
        gray:   { label: 'Sconosciuto', borderColor: 'rgba(71,85,105,0.3)',   color: '#475569', dotAnim: '' },
    };

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
        const filtro = {
            "btn-in-arrivo": filtri.inArrivoProssimi,
            "btn-in-partenza": filtri.inPartenzaProssimi,
            "btn-atterrati": filtri.atterrati,
            "btn-paese": filtri.inPaese
        }[activeBtn?.id];

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
            const cfg = statusConfig[statusClass];
            const card = document.createElement("div");
            card.className = "flight-card";
            card.addEventListener("click", () => window.location.href = `/flights/${flight.id}`);
            card.innerHTML = `
            <div class="flight-card-content">
                <div class="result-status-badge" style="border-color:${cfg.borderColor}; color:${cfg.color};">
                    <span class="result-status-dot" style="background:${cfg.color};"></span>
                    ${cfg.label}
                </div>
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
</script>
</body>
</html>
