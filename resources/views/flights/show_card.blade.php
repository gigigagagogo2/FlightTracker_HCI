    <!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Monitoraggio Volo</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('css/flights/show_card.css') }}" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    </head>
    <body>
    <div class="container flight-monitor mt-5">
        <h2 class="text-center mb-4">Monitoraggio Volo</h2>

        <div class="card shadow-sm p-4 mb-4" style="position: relative;">
            @auth
                @if(!(auth()->user()->is_admin))
                    <div class="position-absolute" style="top: 10px; right: 10px;">
                        <i id="starIcon" class="fa {{ auth()->user()->flights->contains($flight->id) ? 'fa-star' : 'fa-star-o' }}"
                           style="font-size: 2rem; color: gold; cursor: pointer;"></i>
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
            <div id="progress-bar" class="progress-bar bg-warning text-dark fw-bold" role="progressbar" style="width:0%;">
                0%
            </div>
        </div>

        <div id="map" style="height: 500px; width: 100%;"></div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary" style="margin-top:0.8rem ">← Torna alla ricerca</a>
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


            aggiornaVolo();
            setInterval(aggiornaVolo, 10000);
        }
        let rotta;
        function aggiornaVolo() {

            fetch("{{ url('/api/simulazione-volo/' . $flight->id) }}")
                .then(res => res.json())
                .then(data => {
                    const nuovaPosizione = { lat: data.lat, lng: data.lon };

                    marker.setPosition(nuovaPosizione);
                    map.panTo(nuovaPosizione);

                    document.getElementById("current-coordinates").innerText =
                        `${data.lat.toFixed(4)} / ${data.lon.toFixed(4)}`;

                    document.getElementById("current-speed").innerText =
                        `${data.velocita} km/h`;

                    const progressBar = document.getElementById("progress-bar");
                    progressBar.style.width = `${data.percentuale}%`
                    progressBar.innerText = `${Math.round(data.percentuale)}%`;

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
                            strokeColor: "#282828",
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
    <?php
        $googleapi = env("GOOGLE_MAPS_API");
        ?>


    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleapi ?>&callback=initMap" async defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const starIcon = document.getElementById("starIcon");

            if (starIcon) {
                starIcon.addEventListener("click", function () {
                    const flightId = {{ $flight->id }};
                    const checked = !starIcon.classList.contains("fa-star-o");  // Controlla se la stella è piena o vuota
                    const url = checked
                        ? "{{ url('/flights/preferiti/remove') }}"
                        : "{{ url('/flights/preferiti/add') }}";

                    fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ flight_id: flightId })
                    })
                        .then(response => {
                            if (!response.ok) throw new Error("Errore nella richiesta");
                            return response.json();
                        })
                        .then(data => {
                            // Cambia l'icona della stella dopo il click
                            if (checked) {
                                starIcon.classList.remove("fa-star");
                                starIcon.classList.add("fa-star-o");
                            } else {
                                starIcon.classList.remove("fa-star-o");
                                starIcon.classList.add("fa-star");
                            }
                        })
                        .catch(error => {
                            alert("Errore nel salvataggio dei preferiti.");
                        });
                });
            }
        });

    </script>


    </body>
    </html>

