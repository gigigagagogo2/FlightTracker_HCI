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
    <link rel="stylesheet" href="{{ asset('css/navbar.css')}}">
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
                    <input type="text" class="form-control search-input" id="search-input" placeholder="Inserisci volo" name="query" autocomplete="off">
                </div>


            </div>
        </form>
    </div>

    <div class="button-bar ps-3">
        <button class="button secondary" id="btn-in-arrivo">In arrivo</button>
        <button class="button secondary" id="btn-in-partenza">In partenza</button>
        <button class="button secondary" id="btn-atterrati">Atterrati</button>
        <button class="button secondary" id="btn-italia">Italia</button>
    </div>


    <!-- Contenitore per le card statiche iniziali -->
    <div class="row mt-5 px-3" id="cards-container">
        <!-- Card con immagini create da creaCardVolo -->
    </div>

    <!-- Contenitore per i risultati dinamici della ricerca -->
    <div class="container mt-5" id="result-container" style="display: none;">
        <!-- Card ricerca dinamica -->
    </div>

</div>
@include('footer')

<script>

    let isSearchActive = false;


    const input = document.getElementById("search-input");
    const title = document.getElementById("search-title");
    const searchSection = document.getElementById("search-section");
    const resultContainer = document.getElementById("result-container");
    const cancelBtn = document.getElementById("cancel-btn");
    let currentFlights = [];


    let index = 0;
    const words = ["volo", "aereoporto", "citta"];

    function updatePlaceholder() {
        index = (index + 1) % words.length;
        input.setAttribute("placeholder", "Inserisci " + words[index]);
    }

    function resetSearch() {
        isSearchActive = false;
        title.style.display = "block";
        searchSection.classList.remove("search-fixed");
        resultContainer.innerHTML = "";
        input.value = "";
        cancelBtn.classList.add("hidden");
        input.blur();
        document.getElementById("cards-container").style.display = "flex";
        resultContainer.style.display = "none";
        deselectAll();
    }
    function isFilterActive() {
        return document.querySelector(".button.selected") !== null;
    }
    function showResults(data) {
        currentFlights = data;
        title.style.display = "none";
        searchSection.classList.add("search-fixed");
        resultContainer.innerHTML = "";

        if (data.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">Nessun volo trovato.</p>';
            return;
        }

        let filteredData = [...data]; // copia dell'array

        const activeBtn = document.querySelector(".button.selected");

        // Se non c'è filtro attivo (quindi solo ricerca), rimuovi i voli rossi
        if (!activeBtn) {
            filteredData = filteredData.filter(f => f.status !== "red");
        }

        // Se c'è un filtro attivo, applicalo sopra i risultati già filtrati dai rossi (se serve)
        if (activeBtn) {
            const id = activeBtn.id;
            const filtro = {
                "btn-in-arrivo": filtri.inArrivoProssimi,
                "btn-in-partenza": filtri.inPartenzaProssimi,
                "btn-atterrati": filtri.atterrati,
                "btn-italia": filtri.inItalia
            }[id];

            if (filtro) {
                filteredData = filteredData.filter(filtro);
            }
        }

        filteredData.forEach(flight => {
            const statusClass = ['green','yellow', 'red'].includes(flight.status)
                ? flight.status
                : 'gray';


            const card = document.createElement("div");
            card.className = "flight-card custom-shadow";
            card.style.cursor = "pointer";
            card.addEventListener("click", () => {
                window.location.href = `/flights/${flight.id}`;
            });

            const departureCity = flight.departure_airport.city;
            const arrivalCity   = flight.arrival_airport.city;
            const departureTime  = new Date(flight.departure_time).toLocaleString();
            const arrivalTime    = new Date(flight.arrival_time).toLocaleString();
            const planeImage     = flight.airplane_model.image_path;

            card.innerHTML = `
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center position-relative px-4 py-2 w-100" style="min-height:100px; background:white; border-radius:8px;">
                <img src="${planeImage}" alt="Aereo" class="flight-image me-3" style="width:70px; height:auto; flex-shrink:0;">
                <div class="flex-grow-1">
                    <h5 class="mb-1">${departureCity} → ${arrivalCity}</h5>
                    <p class="mb-1 fs-6">${departureTime} - ${arrivalTime}</p>
                    <small>Modello: ${flight.airplane_model.name}</small>
                </div>
                <span class="status-dot ${statusClass} status-dot-absolute"></span>
            </div>
        </div>`;

            resultContainer.appendChild(card);
        });
    }

    function fetchAllFlights() {

        fetch(`/search-flights?query=`)
            .then(r => r.json())
            .then(showResults)
            .catch(err => console.error(err));
    }

    input.addEventListener("focus", () => {
        isSearchActive = true;
        if (!input.value.trim()) fetchAllFlights();

        // Nasconde le card statiche e mostra i risultati dinamici
        document.getElementById("cards-container").style.display = "none";
        resultContainer.style.display = "block";
    });


    input.addEventListener("input", () => {
        const q = input.value.trim();

        // Nasconde le card statiche con immagini e mostra quelle dinamiche
        document.getElementById("cards-container").style.display = "none";
        resultContainer.style.display = "block";

        if (q.length === 0) {
            fetchAllFlights();
            return;
        }
        fetch(`/search-flights?query=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(showResults)
            .catch(err => console.error(err));
    });

    input.addEventListener("keydown", e => {
        if (e.key === "Escape") {
            resetSearch();
        }
    });

    setInterval(updatePlaceholder, 3000);

    // Variabile globale per contenere i voli
    // Variabile globale per contenere i voli
    let voli = [];

    // Funzione generica per creare la card di un volo
    // Funzione generica per creare la card di un volo
    function creaCardVolo(volo, immagineNumero) {
        const partenzaCitta = volo.departure_airport.city;
        const arrivoCitta = volo.arrival_airport.city;

        return `
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm" style="cursor:pointer;">
                <div style="width:100%; height:200px; overflow:hidden;">
                    <img src="/images/city/city${immagineNumero}.jpg" class="card-img-top" alt="Immagine volo" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="card-body">
                    <h5 class="card-title">${partenzaCitta} → ${arrivoCitta}</h5>
                </div>
            </div>
        </div>`;
    }

    function mostraVoliConFiltro(voli, filtro, messaggioNessuno) {
        const container = document.getElementById("cards-container");
        container.innerHTML = "";

        const voliFiltrati = voli.filter(filtro);

        if (voliFiltrati.length === 0) {
            container.innerHTML = `<p class='text-muted'>${messaggioNessuno}</p>`;
            return;
        }

        // Prepara immagini mescolate per evitare ripetizioni immediate
        const immaginiDisponibili = Array.from({ length: 18 }, (_, i) => i + 1);
        const immaginiMescolate = shuffleArray(immaginiDisponibili);

        voliFiltrati.forEach((volo, index) => {
            container.innerHTML += creaCardVolo(volo, immaginiMescolate[index % immaginiMescolate.length]);
        });
    }

    // Definizione dei filtri corretti, usando arrival_time e departure_time
    const filtri = {
        inArrivoProssimi: volo => {
            if (!volo.arrival_time) return false;
            const now = Date.now();
            const at  = new Date(volo.arrival_time).getTime();
            // arrivo entro le prossime 2 ore
            return at >= now && at <= now + 2 * 3600_000;
        },
            inPartenzaProssimi: volo => {
            if (!volo.departure_time) return false;
            const now = Date.now();
            const dt  = new Date(volo.departure_time).getTime();
            // partenza entro le prossime 2 ore
            return dt >= now && dt <= now + 2 * 3600_000;
        },
            atterrati: volo => {
            // il tuo API marca lo status come "red" per atterrato
            return volo.status === "red";
        },
            inItalia: volo => {
            // controlla paese di partenza o arrivo
            const c1 = (volo.departure_airport.country  || "").toLowerCase();
            const c2 = (volo.arrival_airport.country    || "").toLowerCase();
            return ["italia","italy","it"].includes(c1) || ["italia","italy","it"].includes(c2);
        }
    };


    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    // Funzione per deselezionare tutti i bottoni
    function deselectAll() {
        const buttons = document.querySelectorAll(".button");
        buttons.forEach(b => b.classList.remove("selected"));
    }

    function applicaFiltroAttivo() {
        if (currentFlights.length === 0) return;
        showResults(currentFlights);
    }

    const bottoneFiltri = document.querySelectorAll(".button-bar .button");

    bottoneFiltri.forEach(btn => {
        btn.addEventListener("click", () => {
            const isAlreadySelected = btn.classList.contains("selected");

            // Deseleziona tutti i bottoni
            deselectAll();

            if (!isAlreadySelected) {
                // Se NON era già selezionato → lo seleziono ora
                btn.classList.add("selected");
            }

            // Applico filtro in base alla modalità
            if (isSearchActive) {
                applicaFiltroAttivo();
            } else {
                const filtro = {
                    "btn-in-arrivo": filtri.inArrivoProssimi,
                    "btn-in-partenza": filtri.inPartenzaProssimi,
                    "btn-atterrati": filtri.atterrati,
                    "btn-italia": filtri.inItalia
                }[btn.id];

                if (btn.classList.contains("selected") && filtro) {
                    mostraVoliConFiltro(voli, filtro, "Nessun volo corrispondente trovato.");
                } else {
                    mostraVoliConFiltro(voli, () => true, "Nessun volo disponibile.");
                }
            }
        });
    });

    function caricaVoli() {
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => {
                voli = v;
                console.log(`Caricati ${v.length} voli inizialmente`);
                mostraVoliConFiltro(voli, () => true, "Nessun volo disponibile.");
            })
            .catch(err => console.error("Errore caricamento iniziale:", err));
    }

    // Caricamento iniziale dei voli
    document.addEventListener("DOMContentLoaded", () => {
        caricaVoli();
    });

</script>
</body>
</html>
