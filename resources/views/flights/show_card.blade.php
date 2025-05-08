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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

</head>
<body>
<div class="container flight-monitor mt-5">
    <h2 class="text-center mb-4">Monitoraggio Volo</h2>

    <div class="card shadow-sm p-4 mb-4 position-relative">
        @auth
            @if(! auth()->user()->is_admin)
                <div class="position-absolute" style="top:10px; right:10px;">
                    <i id="starIcon" class="{{ auth()->user()->flights->contains($flight->id) ? 'fas' : 'far' }} fa-star"></i>
                </div>
            @endif
        @endauth

        <div class="row align-items-center">
            <div class="col-md-4 text-center">
                <img src="/{{ $flight->airplaneModel->image_path }}" alt="{{ $flight->airplaneModel->name }}" class="airplane-image mb-3">
                <h5>{{ $flight->airplaneModel->name }}</h5>
            </div>
            <div class="col-md-8">
                <div class="info-block mb-3">
                    <strong>Partenza:</strong> {{ $flight->departureAirport->city }} – {{ \Carbon\Carbon::parse($flight->departure_time)->format('d/m/Y H:i') }}<br>
                    <strong>Arrivo:</strong> {{ $flight->arrivalAirport->city }} – {{ \Carbon\Carbon::parse($flight->arrival_time)->format('d/m/Y H:i') }}
                </div>
                <div class="info-block mb-2">
                    <strong>Coordinate attuali:</strong> <span id="current-coordinates">-- / --</span><br>
                    <strong>Velocità attuale:</strong> <span id="current-speed">-- km/h</span>
                </div>
            </div>
        </div>
    </div>

    <div class="progress my-4" style="height: 25px;">
        <div id="progress-bar" class="progress-bar bg-warning text-dark fw-bold" role="progressbar" style="width:0%;">0%</div>
    </div>

    <div id="map" style="height: 500px; width: 100%;"></div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-3">← Torna alla ricerca</a>
    </div>
</div>

<script>
    let map, marker, rotta;
    window.initMap = function() {
        const iniziale = { lat: 0, lng: 0 };
        map = new google.maps.Map(document.getElementById("map"), { zoom: 5, center: iniziale });
        marker = new google.maps.Marker({ position: iniziale, map, title: "Aereo", icon: { url: "/images/icon.svg", scaledSize: new google.maps.Size(40,40), anchor: new google.maps.Point(25,25) }});
        aggiornaVolo(); setInterval(aggiornaVolo, 10000);
    };

    function aggiornaVolo() {
        fetch("{{ url('/api/simulazione-volo/' . $flight->id) }}")
            .then(res => res.json())
            .then(data => {
                const pos = { lat: data.lat, lng: data.lon };
                marker.setPosition(pos); map.panTo(pos);
                document.getElementById("current-coordinates").innerText = `${data.lat.toFixed(4)} / ${data.lon.toFixed(4)}`;
                document.getElementById("current-speed").innerText = `${data.velocita} km/h`;
                const pb = document.getElementById("progress-bar"); pb.style.width = `${data.percentuale}%`; pb.innerText = `${Math.round(data.percentuale)}%`;
                if (!rotta) {
                    const p = { lat: {{ $flight->departureAirport->latitude }}, lng: {{ $flight->departureAirport->longitude }} };
                    const a = { lat: {{ $flight->arrivalAirport->latitude }}, lng: {{ $flight->arrivalAirport->longitude }} };
                    rotta = new google.maps.Polyline({ path: [p,a], geodesic: true, strokeOpacity:0, icons:[{ icon:{ path:'M 0,-1 0,1', strokeOpacity:1, scale:4 }, offset:'0', repeat:'20px' }], map });
                }
            });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const starIcon = document.getElementById("starIcon"); if (!starIcon) return;
        starIcon.addEventListener("click", () => {
            const fav = starIcon.classList.contains("fas");
            const url = fav ? "{{ url('/flights/preferiti/remove') }}" : "{{ url('/flights/preferiti/add') }}";
            fetch(url, { method:"POST", headers:{"Content-Type":"application/json","X-CSRF-TOKEN":"{{ csrf_token() }}"}, body: JSON.stringify({ flight_id: {{ $flight->id }} }) })
                .then(r=>r.json()).then(() => { starIcon.classList.toggle("fas"); starIcon.classList.toggle("far"); })
                .catch(()=>alert("Errore nel salvataggio dei preferiti."));
        });
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API') }}&callback=initMap" async defer></script>
</body>
</html>
