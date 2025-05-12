<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mappa Voli Preferiti</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/user/personal_map.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

</head>
<body>

@include('navbar')

<div class="map-container">
    <h2 class="text-center mb-4">I tuoi voli preferiti</h2>
    <div id="map"></div>
</div>

<div id="flightInfoCard" class="card position-absolute bottom-0 start-50 translate-middle-x mb-4 shadow"
     style="width: 22rem; display: none; z-index: 999;">
    <div class="card-body">
        <h5 class="card-title" id="flightModelName">Modello Aereo</h5>
        <p class="card-text mb-1"><strong>Coordinate:</strong> <span id="flightCoords">-</span></p>
        <p class="card-text mb-2"><strong>Velocità:</strong> <span id="flightSpeed">-</span> km/h</p>
        <div class="progress">
            <div id="flightProgress" class="progress-bar" role="progressbar" style="width: 0"></div>
        </div>
    </div>
</div>

@include('user/notify_popup')
<!-- TODO:sistemare -->
@include('footer')

<script type="module">
    const flights = @json($flights);
    let map;
    /** @type {Object.<number, RotatableOverlay>} */
    let overlays = {};
    let routes = {};
    let updates = 0;
    let currentFlightId = null;

    async function initMap() {
        await google.maps.importLibrary('maps');
        const {spherical} = await google.maps.importLibrary("geometry");

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 5,
            center: {lat: 45, lng: 15},
            streetViewControl: false,

        });

        const module = await import('/js/RotatableOverlay.js');
        const RotatableOverlay = module.default;

        for (const flight of flights) {
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

            const iniziale = new google.maps.LatLng(0, 0);

            const overlay = new RotatableOverlay(
                iniziale,
                '/images/plane-map-icon.svg',
                iconHeading
            );

            overlay.addListener('click', () => {
                // Closure, const value are saved inside the inner function (e.g. flight, overlay)
                currentFlightId = flight.id;
                const data = overlay.flightData;

                if (!data) return;

                document.getElementById('flightInfoCard').style.display = 'block';
                document.getElementById('flightModelName').innerText = flight.airplane_model.name;
                document.getElementById('flightCoords').innerText = `${data.lat.toFixed(2)} , ${data.lng.toFixed(2)}`;
                document.getElementById('flightSpeed').innerText = `${data.speed ?? '-'}`;
                document.getElementById('flightProgress').style.width = `${Math.round(data.progress * 100)}%`;
                document.getElementById('flightProgress').innerText = `${Math.round(data.progress * 100)}%`;
            });

            overlay.setMap(map);
            overlays[flight.id] = overlay;

            // Disegna rotta tratteggiata
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
        }

        let offset = 0;

        setInterval(() => {
            offset = (offset + 0.5) % 20;

            for (const flight of flights) {
                routes[flight.id].set('icons', [{
                    icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, strokeColor: '#000000', scale: 4 },
                    offset: `${offset}px`,
                    repeat: '20px'
                }]);
            }
        }, 50);

        await aggiornaPosizioni();

        setInterval(aggiornaPosizioni, 500);
    }

    let isRequestInProgress = false;

    async function aggiornaPosizioni() {
        if (isRequestInProgress) return;
        isRequestInProgress = true;

        updates++;
        for (const flight of flights) {
            try {
                const res = await fetch(`/api/simulazione-volo/${flight.id}`);
                const data = await res.json();

                const nuovaPosizione = new google.maps.LatLng(data.lat, data.lng);
                overlays[flight.id].setPosition(nuovaPosizione);
                overlays[flight.id].flightData = data;

                // Se questo è il volo attualmente visualizzato nella card, aggiorna anche i dati
                if (flight.id === currentFlightId) {
                    document.getElementById('flightCoords').innerText = `${nuovaPosizione.lat().toFixed(2)}, ${nuovaPosizione.lng().toFixed(2)}`;
                    document.getElementById('flightSpeed').innerText = `${data.speed ?? '-'}`;
                    document.getElementById('flightProgress').style.width = `${Math.round(data.progress * 100)}%`;
                    document.getElementById('flightProgress').innerText = `${Math.round(data.progress * 100)}%`;
                }

                if (data.progress === 0 || data.progress === 1) {
                    overlays[flight.id].setMap(null);
                    routes[flight.id].setMap(null);
                }

            } catch (err) {
                console.error(`Errore aggiornamento volo ${flight.id}:`, err);
            } finally {
                isRequestInProgress = false;
            }
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
