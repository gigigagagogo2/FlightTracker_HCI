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
            <div class="input-group">
                <span class="input-group-text search-icon">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control search-input" id="search-input" placeholder="Inserisci volo" name="query" autocomplete="off">
            </div>
        </form>
    </div>

    <div id="result-container" class="container mt-5">
        <!-- I voli verranno qui -->
    </div>
</div>
@include('user/notify_popup')
@include('footer')
<script>
    const input = document.getElementById("search-input");
    const title = document.getElementById("search-title");
    const searchSection = document.getElementById("search-section");
    const resultContainer = document.getElementById("result-container");

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
            if(statusClass != 'red'){
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
  <div class="d-flex align-items-center w-100 position-relative">
    <!-- Immagine -->
    <img src="${planeImage}" alt="Aereo" class="flight-image me-3" style="width:80px; height:auto; flex-shrink:0;">

    <!-- Info -->
    <div class="flex-grow-1">
      <h5 class="mb-1">${departureCity} → ${arrivalCity}</h5>
      <p class="mb-1">${departureTime} - ${arrivalTime}</p>
      <small>Modello: ${flight.airplane_model.name}</small>
    </div>

    <!-- Pallino posizionato assolutamente -->
    <span class="status-dot ${statusClass} status-dot-absolute"></span>
  </div>
`;

                resultContainer.appendChild(card);
            }
        });
    }

    function fetchAllFlights() {
        fetch(`/search-flights?query=`)
            .then(r => r.json())
            .then(showResults)
            .catch(err => console.error(err));
    }

    input.addEventListener("focus", () => {
        if (!input.value.trim()) fetchAllFlights();
    });

    input.addEventListener("input", () => {
        const q = input.value.trim();
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
            input.value = "";
            resetSearch();
        }
    });

    setInterval(updatePlaceholder, 3000);
</script>
</body>
</html>
