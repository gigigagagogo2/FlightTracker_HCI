<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>La mia mappa – FlightTracker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user/personal_map.css') }}">
</head>
<body class="page-map">

@include('navbar')

<main class="map-main">

    @if(count($flights) === 0)
        <div class="map-empty">
            <div class="map-empty__icon"><i class="fas fa-heart-broken"></i></div>
            <h3 class="map-empty__title">Nessun volo preferito</h3>
            <p class="map-empty__sub">Aggiungi voli ai preferiti per visualizzarli sulla mappa.</p>
            <a href="{{ route('home') }}" class="map-empty__btn">Esplora i voli</a>
        </div>
    @elseif($activeFlightsCount === 0)
        <div class="map-empty">
            <div class="map-empty__icon"><i class="fas fa-plane-slash"></i></div>
            <h3 class="map-empty__title">Nessun volo in corso</h3>
            <p class="map-empty__sub">I tuoi voli preferiti non sono attualmente in volo.</p>
        </div>
    @else
        <div class="map-layout">

            <!-- ── PANNELLO SINISTRO ── -->
            <aside class="flight-panel">

                <!-- VISTA LISTA -->
                <div id="viewList">
                    <div class="panel-header">
                        <div class="flight-badge">
                            <span class="pulse-dot pulse-dot--cyan"></span>
                            Live
                        </div>
                        <h1>La mia <span>mappa</span></h1>
                        <p class="panel-sub">{{ $activeFlightsCount }} volo/i attivo/i — clicca per i dettagli</p>
                    </div>

                    <div class="flight-list" id="flightList">
                        @foreach($flights as $flight)
                            <div class="flight-list-item" data-id="{{ $flight->id }}" onclick="selectFlight({{ $flight->id }})">
                                <div class="fli-icon"><i class="fas fa-plane"></i></div>
                                <div class="fli-info">
                                    <div class="fli-model">{{ $flight->airplaneModel->name }}</div>
                                    <div class="fli-route">
                                        {{ $flight->departureAirport->city }}
                                        <i class="fas fa-arrow-right fli-arrow"></i>
                                        {{ $flight->arrivalAirport->city }}
                                    </div>
                                </div>
                                <button class="fli-remove"
                                        title="Rimuovi dai preferiti"
                                        onclick="event.stopPropagation(); openRemovePopup({{ $flight->id }}, '{{ addslashes($flight->airplaneModel->name) }}')">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- VISTA DETTAGLIO -->
                <div id="viewDetail" style="display:none;">

                    <div class="panel-header">
                        <button class="back-btn" onclick="tornataLista()">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <div class="flight-badge" style="margin-top:0.75rem;">
                            <span class="pulse-dot"></span>
                            In volo
                        </div>
                        <h1>Monitoraggio <span>Volo</span></h1>
                    </div>

                    <!-- Aereo -->
                    <div class="airplane-card">
                        <div class="airplane-img-wrap">
                            <img id="detail-plane-img" src="" alt="">
                        </div>
                        <div class="airplane-info">
                            <h3 id="detail-model">—</h3>
                            <p>AEROMOBILE</p>
                        </div>
                        <button class="detail-remove-btn" id="detail-remove-btn" title="Rimuovi dai preferiti">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>

                    <!-- Rotta -->
                    <div class="route-section">
                        <p class="section-label">Rotta</p>
                        <div class="route-row">
                            <div class="route-airport">
                                <span class="city" id="detail-dep-city">—</span>
                                <span class="time" id="detail-dep-time">—</span>
                            </div>
                            <div class="route-divider">
                                <div class="dot"></div>
                                <div class="line"></div>
                                <i class="fas fa-plane"></i>
                                <div class="line"></div>
                                <div class="dot"></div>
                            </div>
                            <div class="route-airport text-end">
                                <span class="city" id="detail-arr-city">—</span>
                                <span class="time" id="detail-arr-time">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="progress-section">
                        <p class="section-label">Avanzamento volo</p>
                        <div class="progress-header">
                            <span class="progress-city" id="detail-dep-label">—</span>
                            <span class="progress-pct" id="detail-progress-pct">0%</span>
                            <span class="progress-city" id="detail-arr-label">—</span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" id="detail-progress-bar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- Live data -->
                    <div class="live-section">
                        <p class="section-label">Dati in tempo reale</p>
                        <div class="live-grid">
                            <div class="live-card cyan-accent">
                                <p class="card-label">Coordinate</p>
                                <p class="card-value cyan" id="detail-coords">-- / --</p>
                                <i class="fas fa-map-marker-alt card-icon"></i>
                            </div>
                            <div class="live-card cyan-accent">
                                <p class="card-label">Velocità</p>
                                <p class="card-value cyan" id="detail-speed">-- km/h</p>
                                <i class="fas fa-tachometer-alt card-icon"></i>
                            </div>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- ── MAPPA ── -->
            <div class="map-pane">
                <div id="map"></div>
            </div>

        </div>
    @endif

</main>

<!-- POPUP CONFERMA RIMOZIONE -->
<div class="rm-overlay" id="rmOverlay" style="display:none;" onclick="closeRemovePopup()"></div>
<div class="rm-popup" id="rmPopup" style="display:none;">
    <div class="rm-popup__icon"><i class="fas fa-heart-broken"></i></div>
    <h4 class="rm-popup__title">Rimuovi dai preferiti</h4>
    <p class="rm-popup__sub">Stai per rimuovere <strong id="rmFlightName"></strong> dai tuoi preferiti.</p>
    <div class="rm-popup__actions">
        <button class="rm-btn rm-btn--cancel" onclick="closeRemovePopup()">Annulla</button>
        <button class="rm-btn rm-btn--confirm" onclick="confirmRemove()">Rimuovi</button>
    </div>
</div>

<!-- TOAST -->
<div class="map-toast" id="mapToast"></div>

@include('footer')

<script type="module">
    const flights = @json($flights);
    let map;
    let overlays = {};
    let routes   = {};
    let currentFlightId = null;
    let removeFlightId  = null;

    window.selectFlight      = selectFlight;
    window.tornataLista      = tornataLista;
    window.openRemovePopup   = openRemovePopup;
    window.closeRemovePopup  = closeRemovePopup;
    window.confirmRemove     = confirmRemove;

    // ── POPUP RIMOZIONE ──
    function openRemovePopup(id, name) {
        removeFlightId = id;
        document.getElementById('rmFlightName').textContent = name;
        document.getElementById('rmOverlay').style.display  = 'block';
        document.getElementById('rmPopup').style.display    = 'block';
    }

    function closeRemovePopup() {
        removeFlightId = null;
        document.getElementById('rmOverlay').style.display = 'none';
        document.getElementById('rmPopup').style.display   = 'none';
    }

    async function confirmRemove() {
        if (!removeFlightId) return;
        const id = removeFlightId;
        closeRemovePopup();
        try {
            const res = await fetch(`/flights/preferiti/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            if (!res.ok) throw new Error();

            document.querySelector(`.flight-list-item[data-id="${id}"]`)?.remove();
            overlays[id]?.setMap(null); delete overlays[id];
            routes[id]?.setMap(null);   delete routes[id];
            if (currentFlightId === id) tornataLista();

            showToast('Volo rimosso dai preferiti', 'success');

            // Se non ci sono più voli nella lista, mostra empty state
            if (document.querySelectorAll('.flight-list-item').length === 0) {
                document.querySelector('.map-layout').innerHTML = `
                    <div class="map-empty" style="grid-column:1/-1;">
                        <div class="map-empty__icon"><i class="fas fa-plane-slash"></i></div>
                        <h3 class="map-empty__title">Nessun volo in corso</h3>
                        <p class="map-empty__sub">Non hai più voli preferiti attivi.</p>
                    </div>`;
            }
        } catch {
            showToast('Errore durante la rimozione', 'error');
        }
    }

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('mapToast');
        toast.textContent = msg;
        toast.className = `map-toast map-toast--${type} map-toast--show`;
        setTimeout(() => { toast.className = 'map-toast'; }, 2800);
    }

    // ── MAPPA ──
    async function initMap() {
        await google.maps.importLibrary('maps');
        const { spherical } = await google.maps.importLibrary('geometry');
        const module = await import('/js/RotatableOverlay.js');
        const RotatableOverlay = module.default;

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 4,
            center: { lat: 48, lng: 10 },
            streetViewControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            scaleControl: true,
            zoomControl: true,
            gestureHandling: 'greedy',
            minZoom: 2,
            restriction: {
                latLngBounds: { north: 85, south: -85, west: -180, east: 180 },
                strictBounds: true,
            },
            styles: [
                { elementType: 'geometry', stylers: [{ color: '#1a2540' }] },
                { elementType: 'labels.text.fill', stylers: [{ color: '#94a3b8' }] },
                { elementType: 'labels.text.stroke', stylers: [{ color: '#0a0f1e' }] },
                { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0d1b2a' }] },
                { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#475569' }] },
                { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#253354' }] },
                { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#1a2540' }] },
                { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#2d4068' }] },
                { featureType: 'administrative', elementType: 'geometry', stylers: [{ color: '#2d4068' }] },
                { featureType: 'administrative.country', elementType: 'labels.text.fill', stylers: [{ color: '#94a3b8' }] },
                { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit', stylers: [{ visibility: 'off' }] },
            ]
        });

        try {
            const res = await fetch('/api/simulazione-voli', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: flights.map(f => f.id) })
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const simData = await res.json();

            await Promise.all(flights.map(async flight => {
                const data = simData[flight.id];
                if (!data || data.progress === 1) return;

                const startPoint = new google.maps.LatLng(parseFloat(flight.departure_airport.latitude), parseFloat(flight.departure_airport.longitude));
                const endPoint   = new google.maps.LatLng(parseFloat(flight.arrival_airport.latitude),   parseFloat(flight.arrival_airport.longitude));
                const heading    = spherical.computeHeading(startPoint, endPoint);
                const iniziale   = new google.maps.LatLng(data.lat, data.lng);

                const overlay = new RotatableOverlay(iniziale, '/images/plane-map-icon.svg', -45 + heading);
                overlay.setMap(map);
                overlay.addListener('click', () => selectFlight(flight.id));
                overlays[flight.id] = overlay;

                routes[flight.id] = new google.maps.Polyline({
                    path: [startPoint, endPoint], geodesic: true, strokeOpacity: 0, strokeWeight: 2, zIndex: 1,
                    icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, strokeColor: '#f59e0b', scale: 4 }, offset: '0', repeat: '20px' }],
                    map
                });
            }));
        } catch (err) { console.error('Errore simulazione:', err); }

        let offset = 0;
        setInterval(() => {
            offset = (offset + 1) % 20;
            flights.forEach(f => {
                const r = routes[f.id];
                if (!r) return;
                const icons = r.get('icons');
                if (icons?.length) { icons[0].offset = `${offset}px`; r.set('icons', icons); }
            });
        }, 100);

        await aggiornaPosizioni();
        setInterval(aggiornaPosizioni, 10000);
    }

    async function aggiornaPosizioni() {
        try {
            const res = await fetch('/api/simulazione-voli', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: flights.map(f => f.id) })
            });
            if (!res.ok) throw new Error();
            const data = await res.json();

            for (const flightId in data) {
                const fd = data[flightId];
                if (fd.progress === 1) {
                    overlays[flightId]?.setMap(null); routes[flightId]?.setMap(null);
                    delete overlays[flightId]; delete routes[flightId];
                    document.querySelector(`.flight-list-item[data-id="${flightId}"]`)?.remove();
                    continue;
                }
                const pos = new google.maps.LatLng(fd.lat, fd.lng);
                if (overlays[flightId]) { overlays[flightId].setPosition(pos); overlays[flightId].flightData = fd; }
                if (parseInt(flightId) === currentFlightId) aggiornaDettaglio(fd.lat, fd.lng, fd);
            }
        } catch (err) { console.error('Errore aggiornamento:', err); }
    }

    function aggiornaDettaglio(lat, lng, fd) {
        document.getElementById('detail-coords').innerText = `${lat.toFixed(2)}° / ${lng.toFixed(2)}°`;
        document.getElementById('detail-speed').innerText  = `${Math.round(fd.speed ?? 0)} km/h`;
        const pct = Math.round((fd.progress ?? 0) * 100);
        document.getElementById('detail-progress-bar').style.width = `${pct}%`;
        document.getElementById('detail-progress-pct').innerText   = `${pct}%`;
    }

    function formatDate(str) {
        const d = new Date(str);
        return d.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit', year: 'numeric' })
            + ' ' + d.toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
    }

    function highlightFlight(id, lat, lng) {
        for (const fid in overlays) {
            const img = overlays[fid]?.div?.querySelector('img');
            if (!img) continue;
            img.style.filter = parseInt(fid) === parseInt(id)
                ? 'brightness(0) saturate(100%) invert(27%) sepia(99%) saturate(2000%) hue-rotate(340deg) brightness(1.1)'
                : '';
        }
        map.panTo(new google.maps.LatLng(lat, lng));
    }

    function selectFlight(id) {
        const flight = flights.find(f => f.id === id);
        if (!flight) return;
        currentFlightId = id;

        document.querySelectorAll('.flight-list-item').forEach(el => el.classList.remove('active'));
        document.querySelector(`.flight-list-item[data-id="${id}"]`)?.classList.add('active');

        document.getElementById('detail-model').innerText     = flight.airplane_model.name;
        document.getElementById('detail-plane-img').src       = '/' + flight.airplane_model.image_path;
        document.getElementById('detail-dep-city').innerText  = flight.departure_airport.city;
        document.getElementById('detail-arr-city').innerText  = flight.arrival_airport.city;
        document.getElementById('detail-dep-time').innerText  = formatDate(flight.departure_time);
        document.getElementById('detail-arr-time').innerText  = formatDate(flight.arrival_time);
        document.getElementById('detail-dep-label').innerText = flight.departure_airport.city;
        document.getElementById('detail-arr-label').innerText = flight.arrival_airport.city;

        // Collega bottone rimozione dettaglio
        document.getElementById('detail-remove-btn').onclick = () => openRemovePopup(id, flight.airplane_model.name);

        const overlay = overlays[id];
        if (overlay?.flightData) {
            const fd = overlay.flightData;
            aggiornaDettaglio(fd.lat, fd.lng, fd);
            highlightFlight(id, fd.lat, fd.lng);
        } else {
            fetch('/api/simulazione-voli', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: [id] })
            }).then(r => r.json()).then(data => {
                const fd = data[id];
                if (fd) { aggiornaDettaglio(fd.lat, fd.lng, fd); highlightFlight(id, fd.lat, fd.lng); if (overlay) overlay.flightData = fd; }
            }).catch(() => {});
        }

        document.getElementById('viewList').style.display   = 'none';
        document.getElementById('viewDetail').style.display = 'block';
    }

    function tornataLista() {
        currentFlightId = null;
        for (const fid in overlays) {
            const img = overlays[fid]?.div?.querySelector('img');
            if (img) img.style.filter = '';
        }
        document.getElementById('viewDetail').style.display = 'none';
        document.getElementById('viewList').style.display   = 'block';
        document.querySelectorAll('.flight-list-item').forEach(el => el.classList.remove('active'));
    }

    initMap();
</script>

<script>
    (g => {
        var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",
            m=document,b=window;b=b[c]||(b[c]={});
        var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,
            u=()=>h||(h=new Promise(async(f,n)=>{
                await(a=m.createElement("script"));
                e.set("libraries",[...r]+"");
                for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));
                a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));
        d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))
    })({ key: "{{ env('GOOGLE_MAPS_API') }}", v: "weekly" });
</script>

</body>
</html>
