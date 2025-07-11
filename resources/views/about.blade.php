<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi siamo - FlightTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
</head>
<body>

@include('navbar')

<div class="about-hero text-white d-flex align-items-center">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="display-4 fw-bold">Chi siamo</h1>
        <p class="lead">Un progetto nato per connettere le persone con il cielo.</p>
    </div>
</div>

<div class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0" data-aos="zoom-in">
            <img src="{{ asset('images/sky_view.png') }}" alt="Sky view" class="img-fluid rounded shadow">
        </div>

        <div class="col-md-6" data-aos="fade-left">
            <div class="d-flex align-items-center mb-3" >
                <h2 class="ms-2">La nostra visione</h2>
            </div>
            <p>
                FlightTracker è una piattaforma sviluppata con l’obiettivo di rendere il monitoraggio dei voli semplice,
                preciso e accessibile. Nasce dall’unione di competenze tecniche e passione per l’aviazione.
            </p>
            <p>
                Crediamo nella trasparenza delle informazioni, nell'usabilità delle interfacce e nell'affidabilità dei dati.
                Per questo abbiamo realizzato un sistema intuitivo, pensato per utenti di ogni livello, che permette di
                cercare, esplorare e seguire voli in tempo reale.
            </p>
        </div>
    </div>

    <hr class="my-5">

    <div class="text-center mb-5">
        <h2 class="mb-4">Il team</h2>
        <p class="text-muted">Un gruppo di studenti presso l'Università degli Studi di Brescia.</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-12 col-sm-6 col-md-4 text-center" data-aos="flip-left" data-aos-delay="100">
            <img src="{{ asset('images/dev_1.jpg') }}" alt="Robert" class="team-photo rounded-circle mb-2">
            <h5 class="mb-0">Bararu Robert Daniel</h5>
            <small class="text-muted">Studente di Ingegneria Informatica UniBS</small>
        </div>

        <div class="col-12 col-sm-6 col-md-4 text-center" data-aos="flip-left" data-aos-delay="200">
            <img src="{{ asset('images/dev_2.jpg') }}" alt="Samuele" class="team-photo rounded-circle mb-2">
            <h5 class="mb-0">Valperta Samuele</h5>
            <small class="text-muted">Studente di Ingegneria Informatica UniBS</small>
        </div>

        <div class="col-12 col-sm-6 col-md-4 text-center" data-aos="flip-left" data-aos-delay="300">
            <img src="{{ asset('images/dev_3.jpg') }}" alt="Davide" class="team-photo rounded-circle mb-2">
            <h5 class="mb-0">Fejzulla Davide</h5>
            <small class="text-muted">Studente di Ingegneria Informatica UniBS</small>
        </div>

    </div>
</div>

@include('footer')

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        once: true
    });
</script>
</body>
</html>
