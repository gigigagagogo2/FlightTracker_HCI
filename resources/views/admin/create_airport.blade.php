<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Aeroporto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="p-5 bg-light">

<div class="container" style="max-width: 600px;">
    <h2 class="mb-4 text-center">Aggiungi un Aeroporto</h2>

    <div class="mx-auto bg-white border rounded-4 shadow-sm p-4">
        <form id="airportForm" method="POST" action="{{ route('admin.airport.store') }}">
            @csrf
            <div class="mb-3">
                <label for="country" class="form-label">Paese</label>
                <select id="country" name="country" class="form-select" required>
                    <option value="">Seleziona un paese</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Città</label>
                <input type="text" id="city" name="city" class="form-control" placeholder="Inizia a scrivere..." list="city-suggestions" autocomplete="off" required>
                <datalist id="city-suggestions"></datalist>
                <div id="city-error" class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nome aeroporto</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div class="text-center mt-4">
                <a href="{{ route('admin.airports') }}" class="btn btn-danger me-2">
                    <i class="bi bi-arrow-return-left me-1"></i> Annulla
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Aggiungi volo
                </button>
            </div>
        </form>
    </div>
</div>



<script>
    const countrySelect = document.getElementById('country');
    const cityInput = document.getElementById('city');
    const cityList = document.getElementById('city-suggestions')
    const cityError = document.getElementById('city-error');
    const form = document.getElementById('airportForm');
    const apiKey = "{{ env('GOOGLE_MAPS_API') }}";

    let validCities = [];

    // Carica paesi
    fetch('https://countriesnow.space/api/v0.1/countries/positions')
        .then(res => res.json())
        .then(data => {
            data.data.forEach(country => {
                const opt = document.createElement('option');
                opt.value = country.name;
                opt.textContent = country.name;
                countrySelect.appendChild(opt);
            });
        });

    countrySelect.addEventListener('change', function () {
        const selectedCountry = this.value;
        cityInput.value = '';
        cityList.innerHTML = '';
        validCities = [];
        cityError.textContent = '';
        cityInput.placeholder = "Caricamento...";
        document.getElementById('name').value = '';
        fetch('https://countriesnow.space/api/v0.1/countries/cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ country: selectedCountry })
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
                    cityInput.placeholder = "Inizia a scrivere la città...";
                } else {
                    cityInput.placeholder = "Nessuna città disponibile";
                }
            })
            .catch(() => {
                cityInput.placeholder = "Errore nel caricamento";
            });
    });

    cityInput.addEventListener('change', function () {
        const country = countrySelect.value;
        const city = cityInput.value.trim();
        const query = encodeURIComponent(`${city}, ${country}`);

        document.getElementById('name').value = "Aeroporto di " + city;
        console.log('Setto il nome aeroporto: Aeroporto di ' + city);


        fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${query}&key=${apiKey}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'OK') {
                    const location = data.results[0].geometry.location;
                    document.getElementById('latitude').value = location.lat;
                    document.getElementById('longitude').value = location.lng;
                } else {
                    alert('Coordinate non trovate: ' + data.status);
                }
            });
    });

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
