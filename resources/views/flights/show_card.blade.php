@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Monitoraggio Volo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/flights/show_card.css') }}" rel="stylesheet">

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

</head>
<body>
<div class="container flight-monitor mt-5">
    <h2 class="text-center mb-4">Monitoraggio Volo</h2>

    <div class="card shadow-sm p-4 mb-4 position-relative">
        @auth
            @if(! auth()->user()->is_admin)
                <div class="position-absolute" style="top:10px; right:10px;">
                    <i id="starIcon" class="fa-star {{ $flight->isPreferito() ? 'fas' : 'far' }}"></i></div>
            @endif
        @endauth

        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <img src="/{{ $flight->airplaneModel->image_path }}" alt="{{ $flight->airplaneModel->name }}"
                     class="airplane-image mb-3">
                <h5>{{ $flight->airplaneModel->name }}</h5>
            </div>

            <div class="col-md-8">
                <div class="info-block mb-3">
                    <strong>Partenza:</strong> {{ $flight->departureAirport->city }}
                    – {{ Carbon::parse($flight->departure_time)->format('d/m/Y H:i') }}<br>
                    <strong>Arrivo:</strong> {{ $flight->arrivalAirport->city }}
                    – {{ Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') }}
                </div>

                <div class="info-block mb-2">
                    <strong>Coordinate attuali:</strong> <span id="current-coordinates">-- / --</span><br>
                    <strong>Velocità attuale:</strong> <span id="current-speed">-- km/h</span>
                </div>
            </div>
        </div>
    </div>

    <div class="progress my-4" style="height: 25px;">
        <div id="progress-bar" class="progress-bar bg-warning text-dark fw-bold" role="progressbar" style="width:0%;">
            0%
        </div>
    </div>

    <div id="map" style="height: 500px; width: 100%;"></div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-3">← Torna alla ricerca</a>
    </div>
</div>

<script type="module">
    let map;
    /** @type {RotatableOverlay} */
    let overlay;
    let updates = -1;

    async function initMap() {
        await google.maps.importLibrary('maps');

        // Posizione iniziale temporanea
        const iniziale = new google.maps.LatLng(0, 0);

        const mapOptions = {
            zoom: 4, // Abbastanza da vedere tratte europee/nazionali
            center: { lat: 48.0, lng: 10.0 }, // Centro Europa
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            scaleControl: true,
            zoomControl: true,
            gestureHandling: "greedy", // migliora l'interazione su mobile
            minZoom: 2,
            styles: [
                {
                    featureType: "poi",
                    stylers: [{ visibility: "off" }]
                },
                {
                    featureType: "transit",
                    stylers: [{ visibility: "off" }]
                }
            ]
        };

        // Crea mappa
        map = new google.maps.Map(document.getElementById('map'), mapOptions);

        const startLatitude = {{ $flight->departureAirport->latitude }};
        const startLongitude = {{ $flight->departureAirport->longitude }};
        const endLatitude = {{ $flight->arrivalAirport->latitude }};
        const endLongitude = {{ $flight->arrivalAirport->longitude }};

        let startPoint = new google.maps.LatLng(startLatitude, startLongitude);
        let endPoint = new google.maps.LatLng(endLatitude, endLongitude);

        const {spherical} = await google.maps.importLibrary("geometry");

        // Calcolo dell'angolo di direzione
        const heading = spherical.computeHeading(startPoint, endPoint);
        // Adatto l'angolo all'icona dell'aereo
        const iconHeading = -45 + heading

        // Creo overlay dell'aereo
        const module = await import('/js/RotatableOverlay.js');
        const RotatableOverlay = module.default;
        overlay = new RotatableOverlay(
            iniziale,
            '/images/plane-map-icon.svg',
            iconHeading
        );

        overlay.setMap(map);

        // Disegno la rotta
        const partenza = new google.maps.LatLng({{ $flight->departureAirport->latitude }}, {{ $flight->departureAirport->longitude }});
        const arrivo = new google.maps.LatLng({{ $flight->arrivalAirport->latitude }}, {{ $flight->arrivalAirport->longitude }});

        new google.maps.Polyline({
            path: [partenza, arrivo],
            geodesic: true,
            strokeColor: "#000",
            strokeOpacity: 0,
            strokeWeight: 2,
            zIndex: 1,
            icons: [{
                icon: {
                    path: 'M 0,-1 0,1', // tratteggio
                    strokeOpacity: 1,
                    scale: 4
                },
                offset: '0',
                repeat: '20px'
            }],
            map: map
        });

        // Avvia aggiornamenti
        const posizioneIniziale = await aggiornaVolo();
        if (posizioneIniziale) {
            map.panTo(posizioneIniziale);
        }
        setInterval(aggiornaVolo, 500);
    }

    let isRequestInProgress = false;

    async function aggiornaVolo() {
        if (isRequestInProgress) return;
        isRequestInProgress = true;

        try {
            const res = await fetch("{{ url('/api/simulazione-volo/' . $flight->id) }}");
            const data = await res.json();
            updates++;
            const nuovaPosizione = new google.maps.LatLng(data.lat, data.lng);

            overlay.setPosition(nuovaPosizione);

            // Aggiorno l'immagine in diretta ma i dati solo ogni 20 tick (5s)
            if (data.progress * 100 < 5 || data.progress * 100 > 95 || updates % 20 === 0) {
                document.getElementById("current-speed").innerText =
                    `${parseInt(data.speed)} km/h`;
            }

            if (updates % 20 === 0) {
                document.getElementById("current-coordinates").innerText =
                    `${nuovaPosizione.lat().toFixed(4)} / ${nuovaPosizione.lng().toFixed(4)}`;
                const pb = document.getElementById("progress-bar");
                pb.style.width = `${data.progress * 100}%`;
                pb.innerText = `${Math.round(data.progress * 100)}%`;
            }

            return nuovaPosizione;
        } catch (err) {
            console.error("Errore durante la richiesta:", err);
            return null;
        } finally {
            isRequestInProgress = false;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const starIcon = document.getElementById("starIcon");

        if (starIcon) {
            starIcon.addEventListener("click", togglePreferito);
        }
    });

    async function togglePreferito() {
        const starIcon = document.getElementById("starIcon");
        if (!starIcon) return;

        const isFavorito = starIcon.classList.contains("fas");
        const flightId = {{ $flight->id }};

        const url = `{{ url('/flights/preferiti') }}/${flightId}`;

        try {
            const response = await fetch(url, {
                method: isFavorito ? "DELETE" : "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });

            if (!response.ok) throw new Error();

            starIcon.classList.toggle("fas");
            starIcon.classList.toggle("far");
        } catch (error) {
            alert("Errore nel salvataggio dei preferiti.");
        }
    }

    initMap();
</script>

<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__",
            m = document, b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
    })({
        key: "{{ env('GOOGLE_MAPS_API') }}",
        v: "weekly",
    });
</script>

</body>
</html>
