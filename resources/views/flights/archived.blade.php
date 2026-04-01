@php
    use Carbon\Carbon;
    $departure = Carbon::parse($flight->departure_time);
    $arrival   = Carbon::parse($flight->arrival_time);
    $duration  = $arrival->diff($departure);
@endphp

    <!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riepilogo Volo – FlightTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flights/summary.css') }}">
</head>
<body>

@include("navbar")

<main class="summary-main">
    <div class="summary-wrap">

        <!-- Header -->
        <div class="summary-header">
            <div class="summary-badge">
                <span class="pulse-dot pulse-dot--sm"></span>
                Volo completato
            </div>
            <h1 class="summary-title">Riepilogo Volo</h1>
            <p class="summary-sub">{{ $flight->departureAirport->name }} → {{ $flight->arrivalAirport->name }}</p>
        </div>

        <!-- Hero card aereo + rotta -->
        <div class="hero-card">
            <div class="hero-card-inner">
                <div class="hero-plane-wrap">
                    <img src="/{{ $flight->airplaneModel->image_path }}" alt="{{ $flight->airplaneModel->name }}">
                </div>
                <div class="hero-info">
                    <div class="hero-model">{{ $flight->airplaneModel->name }}</div>
                    <div class="route-row">
                        <div class="route-city">
                            <span class="city-name">{{ $flight->departureAirport->city }}</span>
                            <span class="city-time">{{ $departure->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="route-arrow">
                            <div class="dot"></div>
                            <div class="route-arrow-line"></div>
                            <i class="fas fa-plane route-arrow-icon"></i>
                            <div class="route-arrow-line"></div>
                            <div class="dot"></div>
                        </div>
                        <div class="route-city route-city--right">
                            <span class="city-name">{{ $flight->arrivalAirport->city }}</span>
                            <span class="city-time">{{ $arrival->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats grid -->
        <div class="stats-grid">
            <div class="stat-card accent-green">
                <span class="stat-icon stat-icon--green"><i class="fas fa-tachometer-alt"></i></span>
                <div class="stat-label">Velocità media</div>
                <div class="stat-value stat-value--white">{{ round($averageSpeed, 1) }} km/h</div>
            </div>

            <div class="stat-card accent-green">
                <span class="stat-icon stat-icon--green"><i class="fas fa-clock"></i></span>
                <div class="stat-label">Durata volo</div>
                <div class="stat-value stat-value--white">
                    @if($duration->h > 0)
                        {{ $duration->h }}h {{ $duration->i }}m
                    @else
                        {{ $duration->i }} min
                    @endif
                </div>
            </div>

            <div class="stat-card accent-green">
                <span class="stat-icon stat-icon--green"><i class="fas fa-route"></i></span>
                <div class="stat-label">Distanza</div>
                <div class="stat-value stat-value--white">{{ round($distance, 1) }} km</div>
            </div>

            <div class="stat-card accent-green">
                <span class="stat-icon stat-icon--green"><i class="fas fa-map-marker-alt"></i></span>
                <div class="stat-label">Posizione finale</div>
                <div class="stat-value stat-value--white stat-value--coords">
                    Lat: {{ number_format($flight->arrivalAirport->latitude, 4) }} <br> Long: {{ number_format($flight->arrivalAirport->longitude, 4) }}
                </div>
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Torna alla home
        </a>

    </div>
</main>

@include("footer")

</body>
</html>
