<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Aeroporto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
@include("navbar")
<main class="p-5">
<div class="container" style="max-width: 600px;">
    <h2 class="mb-4 text-center">Modifica Aeroporto</h2>

    <div class="mx-auto bg-white border rounded-4 shadow p-4">
        <form id="airportForm" method="POST" action="{{ route('admin.airport.update', $airport->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="country" class="form-label">Paese</label>
                <select id="country" name="country" class="form-select rounded-2" required>
                    <option value="">Seleziona un paese</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Città</label>
                <input type="text" id="city" name="city" class="form-control rounded-2" placeholder="Inizia a scrivere..." list="city-suggestions" autocomplete="off" value="{{ $airport->city }}" required>
                <datalist id="city-suggestions"></datalist>
                <div id="city-error" class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nome aeroporto</label>
                <input type="text" name="name" id="name" class="form-control rounded-2" value="{{ $airport->name }}" required>
            </div>

            <input type="hidden" name="latitude" id="latitude" value="{{ $airport->latitude }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ $airport->longitude }}">

            <div class="text-center mt-4">
                <a href="{{ route('admin.airports') }}" class="btn btn-danger me-2 rounded-2">
                    <i class="bi bi-arrow-return-left me-1"></i> Annulla
                </a>
                <button type="submit" class="btn btn-success rounded-2">
                    <i class="bi bi-pencil-square me-1"></i> Salva modifiche
                </button>
            </div>
        </form>
    </div>
</div>
</main>
@include("footer")

<script>
    const countrySelect = document.getElementById('country');
    const cityInput = document.getElementById('city');
    const cityList = document.getElementById('city-suggestions');
    const cityError = document.getElementById('city-error');
    const form = document.getElementById('airportForm');
    const apiKey = "{{ env('GOOGLE_MAPS_API') }}";
    let validCities = [];

    // Preseleziona il paese corrente
    const currentCountry = "{{ $airport->country }}";
    const currentCity = "{{ $airport->city }}";

    // Carica paesi
    fetch('https://countriesnow.space/api/v0.1/countries/positions')
        .then(res => res.json())
        .then(data => {
            data.data.forEach(country => {
                const opt = document.createElement('option');
                opt.value = country.name;
                opt.textContent = country.name;
                if (country.name === currentCountry) opt.selected = true;
                countrySelect.appendChild(opt);
            });

            // Triggera il caricamento delle città per il paese già selezionato
            loadCities(currentCountry);
        });

    // Carica le città per il paese selezionato
    function loadCities(country) {
        cityList.innerHTML = '';
        validCities = [];
        cityError.textContent = '';
        cityInput.placeholder = "Caricamento città...";

        fetch('https://countriesnow.space/api/v0.1/countries/cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country })
        })
            .then(res => res.json())
            .then(data => {
                if (data && Array.isArray(data.data)) {
                    validCities = data.data;
                    data.data.forEach(city => {
                        const opt = document.createElement('option');
                        opt.value = city;
                        cityList.appendChild(opt);
                    });
                    cityInput.placeholder = "Inizia a scrivere...";
                } else {
                    cityInput.placeholder = "Nessuna città disponibile";
                }
            })
            .catch(() => {
                cityInput.placeholder = "Errore nel caricamento";
            });
    }

    countrySelect.addEventListener('change', function () {
        loadCities(this.value);
        cityInput.value = '';
        cityError.textContent = '';
        document.getElementById('name').value = '';
    });

    // Aggiorna nome e coordinate aeroporto quando la città cambia
    ['input', 'change'].forEach(eventType => {
        cityInput.addEventListener(eventType, function () {
            const country = countrySelect.value;
            const city = cityInput.value.trim();
            if (!city) return;

            document.getElementById('name').value = "Aeroporto di " + city;

            const query = encodeURIComponent(`${city}, ${country}`);
            fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${query}&key=${apiKey}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'OK') {
                        const location = data.results[0].geometry.location;
                        document.getElementById('latitude').value = location.lat;
                        document.getElementById('longitude').value = location.lng;
                    } else {
                        document.getElementById('latitude').value = '';
                        document.getElementById('longitude').value = '';
                    }
                });
        });
    });

    // Validazione alla sottomissione
    form.addEventListener('submit', function (e) {
        const city = cityInput.value.trim();
        cityError.textContent = '';

        if (!validCities.includes(city)) {
            e.preventDefault();
            cityError.textContent = `La città "${city}" non è valida per il paese selezionato.`;
            cityInput.classList.add('is-invalid');
        } else {
            cityInput.classList.remove('is-invalid');
        }
    });
</script>

</body>
</html>
