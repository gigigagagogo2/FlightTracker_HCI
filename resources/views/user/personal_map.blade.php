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

<div id="flightInfoCard" class="card position-absolute bottom-0 start-50 translate-middle-x mb-4 shadow" style="width: 22rem; display: none; z-index: 999;">
    <div class="card-body">
        <h5 class="card-title" id="flightModelName">Modello Aereo</h5>
        <p class="card-text mb-1"><strong>Coordinate:</strong> <span id="flightCoords">-</span></p>
        <p class="card-text mb-2"><strong>Velocità:</strong> <span id="flightSpeed">-</span> km/h</p>
        <div class="progress">
            <div id="flightProgress" class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
    </div>
</div>

@include('user/notify_popup')
<!-- TODO:sistemare -->
@include('footer')

<script>
    const flights = @json($flights);
    console.log(flights);
    let map;
    const markers = {}; // flight.id => marker
    let currentFlightId = null;


    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 4,
            center: { lat: 45, lng: 15 },
            streetViewControl: false,
            //mapTypeControl: false
        });

        // Per ogni volo preferito: carica stato iniziale
        flights.forEach(flight => {
            fetch(`/api/simulazione-volo/${flight.id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.stato === 'In volo') {
                        const posizione = { lat: data.lat, lng: data.lon };

                        const marker = new google.maps.Marker({
                            position: posizione,
                            map: map,
                            title: flight.airplane_model.name,
                            icon: {
                                url: "/images/icon.svg",
                                scaledSize: new google.maps.Size(35, 35),
                                anchor: new google.maps.Point(20, 20)
                            }
                        });

                        marker.addListener("click", () => {
                            currentFlightId = flight.id;
                            document.getElementById('flightInfoCard').style.display = 'block';

                            // Mostra dati nella card
                            document.getElementById('flightModelName').innerText = flight.airplane_model.name;
                            document.getElementById('flightCoords').innerText = `${data.lat.toFixed(2)}, ${data.lon.toFixed(2)}`;
                            document.getElementById('flightSpeed').innerText = `${data.velocita ?? '-'}`;

                            document.getElementById('flightProgress').style.width = `${Math.round(data.percentuale)}%`;
                            document.getElementById('flightProgress').innerText = `${Math.round(data.percentuale)}%`;
                            console.log(data.percentuale);

                        });


                        markers[flight.id] = marker;

                        // Rotta tratteggiata partenza → arrivo
                        const partenza = {
                            lat: parseFloat(flight.departure_airport.latitude),
                            lng: parseFloat(flight.departure_airport.longitude)
                        };

                        const arrivo = {
                            lat: parseFloat(flight.arrival_airport.latitude),
                            lng: parseFloat(flight.arrival_airport.longitude)
                        };

                        new google.maps.Polyline({
                            path: [partenza, arrivo],
                            geodesic: true,
                            strokeColor: "#282828",
                            strokeOpacity: 0,
                            strokeWeight: 2,
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
                })
                .catch(err => {
                    console.error(`Errore iniziale per volo ${flight.id}:`, err);
                });
        });

        setInterval(updateFlightPositions, 1000);
    }



    function updateFlightPositions() {
        flights.forEach(flight => {
            fetch(`/api/simulazione-volo/${flight.id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.stato === 'In volo' && markers[flight.id]) {
                        // Aggiorna la posizione del marker
                        markers[flight.id].setPosition({ lat: data.lat, lng: data.lon });

                        // Se questo è il volo attualmente visualizzato nella card, aggiorna anche i dati
                        if (flight.id === currentFlightId) {
                            document.getElementById('flightCoords').innerText = `${data.lat.toFixed(2)}, ${data.lon.toFixed(2)}`;
                            document.getElementById('flightSpeed').innerText = `${data.velocita ?? '-'}`;

                            document.getElementById('flightProgress').style.width = `${Math.round(data.percentuale)}%`;
                            document.getElementById('flightProgress').innerText = `${Math.round(data.percentuale)}%`;
                            console.log(data.percentuale);
                        }
                    }
                })
                .catch(err => {
                    console.error("Errore nella simulazione volo", err);
                });
        });
    }


</script>


@php $googleapi = env('GOOGLE_MAPS_API'); @endphp
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleapi }}&callback=initMap" async defer></script>

</body>
</html>
