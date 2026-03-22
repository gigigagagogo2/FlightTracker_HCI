@php use Carbon\Carbon;
App::setLocale('it');
Carbon::setLocale('it');
@endphp
    <!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Monitoraggio Volo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/main-content.css') }}" rel="stylesheet">
    <link href="{{ asset('css/flights/show_card.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>
<body class="page-flight-monitor">
@include("navbar")

@php
    $diffInMinutes = Carbon::now()->diffInMinutes($flight->departure_time, false);
@endphp

<div class="flight-layout">

    <!-- ── PANNELLO SINISTRO ── -->
    <aside class="flight-panel">
        <div class="panel-header">
            <div class="flight-badge">
                @if($diffInMinutes <= 0 && $diffInMinutes > -($flight->departure_time->diffInMinutes($flight->arrival_time)))
                    <div class="pulse-dot"></div> IN VOLO
                @elseif($diffInMinutes > 0)
                    <div class="pulse-dot pulse-dot--cyan"></div> IN PARTENZA
                @else
                    ATTERRATO
                @endif
            </div>
            <h1>Monitoraggio <span>Volo</span></h1>
        </div>

        <!-- Aereo -->
        <div class="airplane-card">
            @auth
                @if(! auth()->user()->is_admin)
                    <button class="star-btn">
                        <i id="starIcon" class="fa-star {{ $flight->isPreferito() ? 'fas' : 'far' }}"></i>
                    </button>
                @endif
            @endauth

            <div class="airplane-img-wrap">
                <img src="/{{ $flight->airplaneModel->image_path }}" alt="{{ $flight->airplaneModel->name }}">
            </div>
            <div class="airplane-info">
                <h3>{{ $flight->airplaneModel->name }}</h3>
                <p>AEROMOBILE</p>
            </div>
        </div>

        <!-- Rotta -->
        <div class="route-section">
            <p class="section-label">Rotta</p>
            <div class="route-row">
                <div class="route-airport">
                    <span class="city">{{ $flight->departureAirport->city }}</span>
                    <span class="time">{{ Carbon::parse($flight->departure_time)->translatedFormat('j M, H:i') }}</span>
                </div>
                <div class="route-divider">
                    <div class="dot"></div>
                    <div class="line"></div>
                    <i class="fas fa-plane"></i>
                    <div class="line"></div>
                    <div class="dot"></div>
                </div>
                <div class="route-airport text-end">
                    <span class="city">{{ $flight->arrivalAirport->city }}</span>
                    <span class="time">{{ Carbon::parse($flight->arrival_time)->translatedFormat('j M, H:i') }}</span>
                </div>
            </div>
        </div>

        @if($diffInMinutes > 120)
            <!-- In attesa -->
            <div class="waiting-panel">
                <div class="waiting-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Monitoraggio non disponibile</h3>
                <p>Il monitoraggio sarà attivo nelle 2 ore precedenti alla partenza del volo.</p>
            </div>

        @else
            <!-- Progresso -->
            <div class="progress-section">
                <p class="section-label">Avanzamento volo</p>
                <div class="progress-header">
                    <span class="progress-city">{{ $flight->departureAirport->city }}</span>
                    <span id="progress-pct" class="progress-pct">0%</span>
                    <span class="progress-city">{{ $flight->arrivalAirport->city }}</span>
                </div>
                <div class="progress-track">
                    <div id="progress-bar" class="progress-fill" style="width:0%;"></div>
                </div>
            </div>

            <!-- Dati live -->
            <div class="live-section">
                <p class="section-label">Dati in tempo reale</p>
                <div class="live-grid">
                    <div class="live-card cyan-accent">
                        <p class="card-label">Coordinate</p>
                        <p class="card-value cyan" id="current-coordinates">-- / --</p>
                        <i class="fas fa-map-marker-alt card-icon"></i>
                    </div>
                    <div class="live-card amber-accent">
                        <p class="card-label">Velocità</p>
                        <p class="card-value amber" id="current-speed">-- km/h</p>
                        <i class="fas fa-tachometer-alt card-icon"></i>
                    </div>
                </div>
            </div>
        @endif
    </aside>

    <!-- ── MAPPA ── -->
    <div class="map-pane">
        @if($diffInMinutes <= 120)
            <div id="map"></div>
        @else
            <div class="map-unavailable">
                <i class="fas fa-map"></i>
                <p>MAPPA NON DISPONIBILE</p>
            </div>
        @endif
    </div>

</div>

@include("footer")

<script type="module">
    let map, overlay, route;

    async function initMap() {
        await google.maps.importLibrary('maps');
        const {spherical} = await google.maps.importLibrary("geometry");
        const module = await import('/js/RotatableOverlay.js');
        const RotatableOverlay = module.default;

        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: {lat: 48.0, lng: 10.0},
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            fullscreenControl: false,
            mapTypeControl: false,
            scaleControl: false,
            zoomControl: true,
            gestureHandling: "greedy",
            minZoom: 2,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#1a2540" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#94a3b8" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#0a0f1e" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#0d1b2a" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#475569" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#253354" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#1a2540" }] },
                { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#2d4068" }] },
                { featureType: "administrative", elementType: "geometry", stylers: [{ color: "#2d4068" }] },
                { featureType: "administrative.country", elementType: "labels.text.fill", stylers: [{ color: "#94a3b8" }] },
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] },
            ]
        });

        const startPoint = new google.maps.LatLng(
            {{ $flight->departureAirport->latitude }},
            {{ $flight->departureAirport->longitude }}
        );
        const endPoint = new google.maps.LatLng(
            {{ $flight->arrivalAirport->latitude }},
            {{ $flight->arrivalAirport->longitude }}
        );

        new google.maps.Marker({
            position: startPoint, map,
            icon: { path: google.maps.SymbolPath.CIRCLE, scale: 6, fillColor: '#f59e0b', fillOpacity: 1, strokeColor: '#0a0f1e', strokeWeight: 2 },
        });
        new google.maps.Marker({
            position: endPoint, map,
            icon: { path: google.maps.SymbolPath.CIRCLE, scale: 6, fillColor: '#22d3ee', fillOpacity: 1, strokeColor: '#0a0f1e', strokeWeight: 2 },
        });

        const heading = spherical.computeHeading(startPoint, endPoint);
        const iconHeading = -45 + heading;

        const data = await fetchFlightData();
        if (!data || data.progress === 1) return;

        const iniziale = new google.maps.LatLng(data.lat, data.lng);
        overlay = new RotatableOverlay(iniziale, '/images/plane-map-icon.svg', iconHeading);
        overlay.setMap(map);

        route = new google.maps.Polyline({
            path: [startPoint, endPoint],
            geodesic: true,
            strokeOpacity: 0,
            strokeWeight: 2,
            zIndex: 1,
            icons: [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, strokeColor: '#f59e0b', scale: 4 }, offset: '0', repeat: '20px' }],
            map
        });

        map.panTo(iniziale);

        let offset = 0;
        setInterval(() => {
            offset = (offset + 0.5) % 20;
            route.set('icons', [{ icon: { path: 'M 0,-1 0,1', strokeOpacity: 1, strokeColor: '#f59e0b', scale: 4 }, offset: `${offset}px`, repeat: '20px' }]);
        }, 100);

        await aggiornaPosizione();
        setInterval(aggiornaPosizione, 10000);
    }

    async function fetchFlightData() {
        try {
            const res = await fetch("{{ url('/api/simulazione-voli') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ ids: [{{ $flight->id }}] }),
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const allData = await res.json();
            return allData[{{ $flight->id }}] || null;
        } catch (err) {
            console.error("Errore fetch:", err);
            return null;
        }
    }

    async function aggiornaPosizione() {
        try {
            const data = await fetchFlightData();
            if (!data) return;

            if (data.progress === 1) {
                if (overlay) overlay.setMap(null);
                if (route) route.setMap(null);
                overlay = null; route = null;
                return;
            }

            const pos = new google.maps.LatLng(data.lat, data.lng);
            overlay.setPosition(pos);

            document.getElementById("current-coordinates").innerText = `${pos.lat().toFixed(2)}° / ${pos.lng().toFixed(2)}°`;
            document.getElementById("current-speed").innerText = `${Math.round(data.speed)} km/h`;

            const percent = Math.round(data.progress * 100);
            document.getElementById("progress-bar").style.width = `${percent}%`;
            document.getElementById("progress-pct").innerText = `${percent}%`;
        } catch (err) {
            console.error("Errore aggiornamento:", err);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const starIcon = document.getElementById("starIcon");
        if (starIcon) {
            starIcon.closest('button').addEventListener("click", togglePreferito);
        }
    });

    async function togglePreferito() {
        const starIcon = document.getElementById("starIcon");
        if (!starIcon) return;
        const isFavorito = starIcon.classList.contains("fas");
        const url = `{{ url('/flights/preferiti') }}/{{ $flight->id }}`;
        try {
            const response = await fetch(url, {
                method: isFavorito ? "DELETE" : "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" }
            });
            if (!response.ok) throw new Error();
            starIcon.classList.toggle("fas");
            starIcon.classList.toggle("far");
        } catch {
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
