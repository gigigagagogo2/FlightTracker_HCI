<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/main-content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>
<body>

@include("navbar")

<main class="main-content">
    <div class="container py-5">
        <h1 class="text-center mb-5">Pannello di Controllo</h1>

        <div class="row justify-content-center gap-4">
            <div class="col-md-4">
                <div class="card text-center shadow-sm admin-card p-4">
                    <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Gestisci Utenti</h5>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary mt-3">Vai</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-sm admin-card p-4">
                    <i class="bi bi-airplane-engines display-4 text-success mb-3"></i>
                    <h5 class="card-title">Gestisci Voli</h5>
                    <a href="{{ route('admin.flights') }}" class="btn btn-outline-success mt-3">Vai</a>
                </div>
            </div>
        </div>
    </div>
</main>

@include("footer")

</body>
</html>

