<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mappa Voli Preferiti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/user/personal_map.css') }}" rel="stylesheet">

</head>
<body>

@include('navbar')
<main class="main-content">
    <div class="map-container">
        <h2 class="text-center mb-4">I tuoi voli preferiti</h2>
        <div id="map"></div>
    </div>

    <div id="flightInfoCard" class="card position-absolute bottom-0 start-50 translate-middle-x mb-4 shadow"
         style="width: 24rem; display: none; z-index: 999;">
        <div class="card-body position-relative">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" aria-label="Chiudi"
                    onclick="chiudiCard()"></button>
            <h5 class="card-title" id="flightModelName">Modello Aereo</h5>
            <p class="card-text mb-1"><strong>Aeroporto di partenza:</strong> <span id="departureAirport">-</span></p>
            <p class="card-text mb-1"><strong>Aeroporto di arrivo:</strong> <span id="arrivalAirport">-</span></p>
            <p class="card-text mb-1"><strong>Coordinate:</strong> <span id="flightCoords">-</span></p>
            <p class="card-text mb-2"><strong>Velocità:</strong> <span id="flightSpeed">-</span> km/h</p>
            <div class="progress">
                <div id="flightProgress" class="progress-bar" role="progressbar" style="width: 0"></div>
            </div>
        </div>
    </div>

    {{--    @include('user/notify_popup')--}}
    @include('footer')

    <script type="module">
        const flights = @json($flights);
        let map;
        /** @type {Object.<number, RotatableOverlay>} */
        let overlays = {};
        let routes = {};
        let currentFlightId = null;

        async function initMap() {
            await google.maps.importLibrary('maps');
            const {spherical} = await google.maps.importLibrary("geometry");

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: {lat: 48, lng: 10},
                streetViewControl: false,
                fullscreenControl: false,
                mapTypeControl: false,
                scaleControl: true,
                zoomControl: true,
                gestureHandling: "greedy", // migliora l'interazione su mobile
                minZoom: 2,
            });

            const module = await import('/js/RotatableOverlay.js');
            const RotatableOverlay = module.default;

            try {
                const res = await fetch('/api/simulazione-voli', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ids: flights.map(f => f.id)})
                });

                if (!res.ok) throw new Error(`Errore HTTP ${res.status}`);
                const flightSimData = await res.json(); // Oggetto con chiave = flight.id


                const flightPromises = flights.map(async (flight) => {
                    const data = flightSimData[flight.id];

                    if (!data || data.progress === 1) {
                        return;
                    }

                    const startPoint = new google.maps.LatLng(
                        parseFloat(flight.departure_airport.latitude),
                        parseFloat(flight.departure_airport.longitude)
                    );

                    const endPoint = new google.maps.LatLng(
                        parseFloat(flight.arrival_airport.latitude),
                        parseFloat(flight.arrival_airport.longitude)
                    );

                    const heading = spherical.computeHeading(startPoint, endPoint);
                    const iconHeading = -45 + heading;

                    const iniziale = new google.maps.LatLng(data.lat, data.lng);

                    const overlay = new RotatableOverlay(
                        iniziale,
                        '/images/plane-map-icon.svg',
                        iconHeading,
                    );

                    overlay.flightData = data;

                    overlay.addListener('click', () => {
                        currentFlightId = flight.id;

                        document.getElementById('flightInfoCard').style.display = 'block';
                        document.getElementById('flightModelName').innerText = flight.airplane_model.name;
                        document.getElementById('departureAirport').innerText = flight.departure_airport.name.split(" ").slice(2).join(" ");
                        document.getElementById('arrivalAirport').innerText = flight.arrival_airport.name.split(" ").slice(2).join(" ");
                        document.getElementById('flightCoords').innerText = `${data.lat.toFixed(2)} , ${data.lng.toFixed(2)}`;
                        document.getElementById('flightSpeed').innerText = `${data.speed ?? '-'}`;
                        document.getElementById('flightProgress').style.width = `${Math.round(data.progress * 100)}%`;
                        document.getElementById('flightProgress').innerText = `${Math.round(data.progress * 100)}%`;
                    });

                    overlay.setMap(map);
                    overlays[flight.id] = overlay;

                    routes[flight.id] = new google.maps.Polyline({
                        path: [startPoint, endPoint],
                        geodesic: true,
                        strokeColor: "#000",
                        strokeOpacity: 0,
                        strokeWeight: 2,
                        zIndex: 1,
                        icons: [{
                            icon: {
                                path: 'M 0,-1 0,1',
                                strokeOpacity: 1,
                                scale: 4
                            },
                            offset: '0',
                            repeat: '20px'
                        }],
                        map: map
                    });
                });

                await Promise.all(flightPromises);
            } catch (err) {
                console.error("Errore nella simulazione dei voli:", err);
            }

            let offset = 0;

            setInterval(() => {
                offset = (offset + 1) % 20;

                flights.forEach(flight => {
                    const route = routes[flight.id];
                    if (!route) return;

                    const icons = route.get('icons');
                    if (!icons || icons.length === 0) return;

                    icons[0].offset = `${offset}px`;
                    route.set('icons', icons);
                });
            }, 100);

            await aggiornaPosizioni();
            setInterval(aggiornaPosizioni, 10000);
        }


        async function aggiornaPosizioni() {
            try {

                // Estrai gli ID dei voli da tracciare
                const ids = flights.map(f => f.id);

                // Invia richiesta POST con tutti gli ID
                const res = await fetch('/api/simulazione-voli', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ids})
                });

                if (!res.ok) throw new Error(`Errore HTTP ${res.status}`);
                const data = await res.json();

                for (const flightId in data) {
                    const flightData = data[flightId];

                    if (flightData.progress === 1) {
                        if (overlays[flightId]) overlays[flightId].setMap(null);
                        if (routes[flightId]) routes[flightId].setMap(null);
                        delete overlays[flightId];
                        delete routes[flightId];
                        continue;
                    }

                    const nuovaPosizione = new google.maps.LatLng(flightData.lat, flightData.lng);

                    if (overlays[flightId]) {
                        overlays[flightId].setPosition(nuovaPosizione);
                        overlays[flightId].flightData = flightData;
                    }

                    if (parseInt(flightId) === currentFlightId) {
                        document.getElementById('flightCoords').innerText = `${nuovaPosizione.lat().toFixed(2)}, ${nuovaPosizione.lng().toFixed(2)}`;
                        document.getElementById('flightSpeed').innerText = `${flightData.speed ?? '-'}`;
                        document.getElementById('flightProgress').style.width = `${Math.round(flightData.progress * 100)}%`;
                        document.getElementById('flightProgress').innerText = `${Math.round(flightData.progress * 100)}%`;
                    }
                }

            } catch (err) {
                console.error('Errore aggiornamento posizioni:', err);
            }
        }

        initMap();

    </script>

    <script>
        function chiudiCard() {
            document.getElementById('flightInfoCard').style.display = 'none';
        }
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
</main>
</body>
</html>
