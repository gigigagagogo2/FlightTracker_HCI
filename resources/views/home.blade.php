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

                <button type="button" class="cancel-button hidden" id="cancel-btn">
                    <i class="bi bi-x-circle-fill me-1"></i>
                    Annulla
                </button>
            </div>

            <div class="checkbox-container">
                <label class="toggle-switch">
                    <input type="checkbox" id="show-landed" name="show_landed">
                    <span class="slider"></span>
                    <span class="toggle-label">Mostra voli atterrati</span>
                </label>
            </div>
        </form>
    </div>

    <div class="button-bar ps-3">
        <button class="button secondary" id="btn-in-arrivo">In arrivo</button>
        <button class="button secondary" id="btn-in-partenza">In partenza</button>
        <button class="button secondary" id="btn-atterrati">Atterrati</button>
        <button class="button secondary" id="btn-italia">Italia</button>
    </div>


    <div class="row mt-5 ps-3" id="cards-container">
        <div id="result-container" class="container mt-5">
            <!-- I voli verranno qui -->
        </div>
        <!-- Fine esempio scheda -->
    </div>



</div>
@include('user/notify_popup')
@include('footer')

<script>
    const input = document.getElementById("search-input");
    const title = document.getElementById("search-title");
    const searchSection = document.getElementById("search-section");
    const resultContainer = document.getElementById("result-container");
    const cancelBtn = document.getElementById("cancel-btn");
    const showLandedCheckbox = document.getElementById("show-landed");

    let index = 0;
    const words = ["volo", "aereoporto", "citta"];

    function updatePlaceholder() {
        index = (index + 1) % words.length;
        input.setAttribute("placeholder", "Inserisci " + words[index]);
    }

    function resetSearch() {
        title.style.display = "block";  // Mostra di nuovo il titolo
        searchSection.classList.remove("search-fixed");  // Rimuove la classe fissa
        resultContainer.innerHTML = "";
        input.value = "";  // Pulisce l'input
        showLandedCheckbox.checked = false;  // Deseleziona il checkbox
        cancelBtn.classList.add("hidden");  // Nasconde il bottone
        input.blur();
    }

    function showResults(data) {
        title.style.display = "none";
        searchSection.classList.add("search-fixed");
        resultContainer.innerHTML = "";

        if (data.length === 0) {
            resultContainer.innerHTML = '<p class="text-center text-muted">Nessun volo trovato.</p>';
            return;
        }

        data.forEach(flight => {
            const statusClass = ['green','yellow', 'red'].includes(flight.status)
                ? flight.status
                : 'gray';

            // Filtra voli atterrati se il checkbox non è selezionato
            if(statusClass === 'red' && !showLandedCheckbox.checked){
                return;
            }

            const card = document.createElement("div");
            card.className = "flight-card mb-3 p-3 border";
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
    <div class="d-flex align-items-center position-relative px-4 py-4" style="width:100vw; min-height:140px; background:white; border-radius:8px; margin-left:-16px; margin-right:-16px;">
        <!-- Immagine -->
        <img src="${planeImage}" alt="Aereo" class="flight-image me-4" style="width:100px; height:auto; flex-shrink:0;">

        <!-- Info -->
        <div class="flex-grow-1">
            <h4 class="mb-2">${departureCity} → ${arrivalCity}</h4>
            <p class="mb-1 fs-5">${departureTime} - ${arrivalTime}</p>
            <small>Modello: ${flight.airplane_model.name}</small>
        </div>

        <!-- Pallino posizionato assolutamente -->
        <span class="status-dot ${statusClass} status-dot-absolute"></span>
    </div>
`;



            resultContainer.appendChild(card);
        });
    }

    function fetchAllFlights() {
        const showLanded = showLandedCheckbox.checked ? '1' : '0';
        fetch(`/search-flights?query=&show_landed=${showLanded}`)
            .then(r => r.json())
            .then(showResults)
            .catch(err => console.error(err));
    }

    input.addEventListener("focus", () => {
        if (!input.value.trim()) fetchAllFlights();
    });

    input.addEventListener("input", () => {
        const q = input.value.trim();
        const showLanded = showLandedCheckbox.checked ? '1' : '0';

        if (q.length === 0) {
            fetchAllFlights();
            return;
        }
        fetch(`/search-flights?query=${encodeURIComponent(q)}&show_landed=${showLanded}`)
            .then(r => r.json())
            .then(showResults)
            .catch(err => console.error(err));
    });

    input.addEventListener("keydown", e => {
        if (e.key === "Escape") {
            resetSearch();
        }
    });

    // Event listener per il bottone Annulla - MODIFICATO
    cancelBtn.addEventListener("click", () => {
        // Invece di resettare tutto, deseleziona solo il checkbox
        showLandedCheckbox.checked = false;
        cancelBtn.classList.add("hidden");  // Nasconde il bottone

        // Aggiorna i risultati con la nuova selezione
        if (searchSection.classList.contains("search-fixed")) {
            const q = input.value.trim();
            const showLanded = '0'; // Ora è sempre 0 perché abbiamo deselezionato

            if (q.length === 0) {
                fetchAllFlights();
            } else {
                fetch(`/search-flights?query=${encodeURIComponent(q)}&show_landed=${showLanded}`)
                    .then(r => r.json())
                    .then(showResults)
                    .catch(err => console.error(err));
            }
        }
    });

    // Event listener per il checkbox
    showLandedCheckbox.addEventListener("change", () => {
        if (showLandedCheckbox.checked) {
            cancelBtn.classList.remove("hidden");  // Mostra il bottone quando è selezionato
        } else {
            cancelBtn.classList.add("hidden");  // Nasconde il bottone quando è deselezionato
        }

        if (searchSection.classList.contains("search-fixed")) {
            fetchAllFlights();
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

    // Event listeners per i bottoni - APPROCCIO ORIGINALE CHE FUNZIONAVA
    document.getElementById("btn-in-arrivo").addEventListener("click", () => {
        deselectAll();
        document.getElementById("btn-in-arrivo").classList.add("selected");
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => mostraVoliConFiltro(v, filtri.inArrivoProssimi, "Nessun volo in arrivo entro 2 ore."))
            .catch(err => console.error(err));
    });

    document.getElementById("btn-in-partenza").addEventListener("click", () => {
        deselectAll();
        document.getElementById("btn-in-partenza").classList.add("selected");
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => mostraVoliConFiltro(v, filtri.inPartenzaProssimi, "Nessun volo in partenza entro 2 ore."))
            .catch(err => console.error(err));
    });

    document.getElementById("btn-atterrati").addEventListener("click", () => {
        deselectAll();
        document.getElementById("btn-atterrati").classList.add("selected");
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => mostraVoliConFiltro(v, filtri.atterrati, "Nessun volo atterrato disponibile."))
            .catch(err => console.error(err));
    });

    document.getElementById("btn-italia").addEventListener("click", () => {
        deselectAll();
        document.getElementById("btn-italia").classList.add("selected");
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => mostraVoliConFiltro(v, filtri.inItalia, "Nessun volo in partenza o arrivo in Italia."))
            .catch(err => console.error(err));
    });
    function caricaVoli() {
        fetch("/search-flights?query=")
            .then(r => r.json())
            .then(v => {
                console.log(`Caricati ${v.length} voli inizialmente`);
                // Puoi mostrare tutti i voli o lasciare vuoto
                // mostraVoliConFiltro(v, () => true, "Nessun volo disponibile.");
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
