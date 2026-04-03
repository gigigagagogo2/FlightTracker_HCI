<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi siamo – FlightTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
</head>
<body>

@include('navbar')

<main class="about-main">

    <!-- ── HERO ── -->
    <section class="about-hero">
        <div class="about-hero__content">
            <div class="about-badge">
                <span class="pulse-dot pulse-dot--cyan"></span>
                Chi siamo
            </div>
            <h1 class="about-hero__title">Voli in tempo <span>reale</span></h1>
            <p class="about-hero__sub">Un progetto nato per avvicinare le persone al cielo.</p>
        </div>
        <div class="about-hero__img">
            <img src="{{ asset('images/sky_view.png') }}" alt="Vista dal cielo">
        </div>
    </section>

    <!-- ── VISIONE ── -->
    <section class="about-section">
        <div class="about-section__label">La nostra visione</div>
        <div class="about-vision">
            <div class="about-vision__card">
                <div class="about-vision__icon"><i class="fas fa-eye"></i></div>
                <h3>Semplicità</h3>
                <p>Cercare e seguire un volo deve essere facile. Per tutti, non solo per gli esperti.</p>
            </div>
            <div class="about-vision__card">
                <div class="about-vision__icon"><i class="fas fa-bolt"></i></div>
                <h3>Dati in tempo reale</h3>
                <p>Posizione, velocità e rotta aggiornati ogni pochi secondi. Sempre precisi.</p>
            </div>
            <div class="about-vision__card">
                <div class="about-vision__icon"><i class="fas fa-lock"></i></div>
                <h3>Affidabilità</h3>
                <p>Un sistema stabile e sicuro. I dati ci sono quando ne hai bisogno.</p>
            </div>
        </div>
    </section>

    <!-- ── TEAM ── -->
    <section class="about-section">
        <div class="about-section__label">Il team</div>
        <h2 class="about-section__title">Chi ha costruito FlightTracker</h2>
        <p class="about-section__sub">Tre studenti di Ingegneria Informatica all'Università di Brescia.</p>

        <div class="about-team">

            <div class="about-team__card">
                <div class="about-team__photo-wrap">
                    <img src="{{ asset('images/robert.jpg') }}" alt="Robert">
                </div>
                <div class="about-team__name">Bararu Robert Daniel</div>
                <div class="about-team__role">Ingegneria Informatica · UniBS</div>
            </div>

            <div class="about-team__card">
                <div class="about-team__photo-wrap">
                    <img src="{{ asset('images/silvio.jpeg') }}" alt="Samuele">
                </div>
                <div class="about-team__name">Sivieri Leonardo</div>
                <div class="about-team__role">Ingegneria Informatica · UniBS</div>
            </div>

            <div class="about-team__card">
                <div class="about-team__photo-wrap">
                    <img src="{{ asset('images/davide.jpg') }}" alt="Davide">
                </div>
                <div class="about-team__name">Fejzulla Davide</div>
                <div class="about-team__role">Ingegneria Informatica · UniBS</div>
            </div>

        </div>
    </section>

</main>

@include('footer')

</body>
</html>
