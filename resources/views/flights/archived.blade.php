@php
    use Carbon\Carbon;
@endphp

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Riepilogo Volo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/flights/show_card.css') }}" rel="stylesheet">

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
</head>
<body>

@include("navbar")

<div class="container flight-summary mt-5">
    <h2 class="text-center mb-4">Riepilogo Volo</h2>

    <!-- CONTENITORE PRINCIPALE -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm p-4 mb-4 position-relative">

                <!-- Sezione Aereo -->
                <div class="row align-items-center mb-4">
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
                    </div>
                </div>

                <!-- Statistiche del Volo -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-tachometer-alt fa-2x text-primary mb-2"></i>
                                <h5>Velocità Media</h5>
                                <h3 id="average-speed" class="text-primary">
                                    {{ round($averageSpeed, 1) . " Km/h"}}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x text-success mb-2"></i>
                                <h5>Durata Volo</h5>
                                <h3 id="total-time" class="text-success">
                                    @php
                                        $departure = Carbon::parse($flight->departure_time);
                                        $arrival = Carbon::parse($flight->arrival_time);
                                        $duration = $arrival->diff($departure);
                                        $hours = $duration->h;
                                        $minutes = $duration->i;

                                        if ($hours > 0) {
                                            echo $hours . 'h ' . $minutes . 'm';
                                        } else {
                                            echo $minutes . ' minuti';
                                        }
                                    @endphp
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informazioni Aggiuntive -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                <h5>Posizione Finale</h5>
                                <p id="final-coordinates" class="mb-0">
                                    {{ number_format($flight->arrivalAirport->latitude, 4) }} /
                                    {{ number_format($flight->arrivalAirport->longitude, 4) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-route fa-2x text-info mb-2"></i>
                                <h5>Distanza Percorsa</h5>
                                <p id="distance-traveled" class="mb-0">
                                    {{ round($distance, 1) . " Km"}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include("footer")


</body>
</html>
