<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Monitoraggio Volo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/flights/show_card.css') }}" rel="stylesheet">
</head>
<body>
<div class="container flight-monitor mt-5">
    <h2 class="text-center mb-4">Monitoraggio Volo</h2>

    <div class="card shadow-sm p-4 mb-4">
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
    <div id="map" style="height: 500px; width: 100%;"></div>

    <div class="text-center">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">← Torna alla ricerca</a>
    </div>
</div>

<script>
    let map;
    let marker;

    function initMap() {
        // Posizione iniziale temporanea
        const iniziale = { lat: 0, lng: 0 };

        // Crea mappa
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 5,
            center: iniziale,
        });

        // Crea marker
        marker = new google.maps.Marker({
            position: iniziale,
            map: map,
            title: "Aereo",
            icon: {
                url: "/images/icon.svg",
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(25, 25)
            }
        });

        // Avvia aggiornamenti
        aggiornaVolo();
        setInterval(aggiornaVolo, 10000);
    }
    let rotta;
    let previousPosition = null;
    function aggiornaVolo() {

        fetch("{{ url('/api/simulazione-volo/' . $flight->id) }}")
            .then(res => res.json())
            .then(data => {
                const nuovaPosizione = { lat: data.lat, lng: data.lon };

                if (previousPosition) {
                    const angle = calcolaAngolo(previousPosition, nuovaPosizione);
                    ruotaAereo(angle); // Ruota l'icona dell'aereo
                }

                marker.setPosition(nuovaPosizione);
                map.panTo(nuovaPosizione);

                document.getElementById("current-coordinates").innerText =
                    `${data.lat.toFixed(4)} / ${data.lon.toFixed(4)}`;

                document.getElementById("current-speed").innerText =
                    `${data.velocita} km/h`;

                if (!rotta) {
                    const partenza = {
                        lat: {{ $flight->departureAirport->latitude }},
                        lng: {{ $flight->departureAirport->longitude }}
                    };

                    const arrivo = {
                        lat: {{ $flight->arrivalAirport->latitude }},
                        lng: {{ $flight->arrivalAirport->longitude }}
                    };

                    rotta = new google.maps.Polyline({
                        path: [partenza, arrivo],
                        geodesic: true,
                        strokeColor: "#000",
                        strokeOpacity: 0,
                        strokeWeight: 2,
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
                }

            })
            .catch(err => {
                console.error("Errore durante la richiesta:", err);
            });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBIxlklVv19VSwkQYlH7RCP4MFjNv9ghQE&callback=initMap" async defer></script>
</body>
</html>

