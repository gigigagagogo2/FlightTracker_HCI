<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Aeroporto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
@include("navbar")
<main class="p-5">
<div class="container" style="max-width: 600px;">
    <h2 class="mb-4 text-center">Aggiungi un aeroporto</h2>

    <div class="mx-auto bg-white border rounded-4 shadow-sm p-4">
        <form id="airportForm" method="POST" action="{{ route('admin.airport.store') }}">
            @csrf

            <div class="mb-3">
                <label for="country" class="form-label">Paese</label>
                <input id="country" name="country" type="text" class="form-control" placeholder="Scrivi un paese..." autocomplete="off" required>
                <div id="country-error" class="invalid-feedback" aria-live="polite"></div>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Città</label>
                <input type="text" id="city" name="city" class="form-control" placeholder="Scrivi una città..." autocomplete="off" required>
                <div id="city-error" class="invalid-feedback" aria-live="polite"></div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nome aeroporto</label>
                <input type="text" name="name" id="name" class="form-control" readonly required>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div class="text-center mt-4">
                <a href="{{ route('admin.airports') }}" class="btn btn-danger me-2">
                    <i class="bi bi-arrow-return-left me-1"></i> Annulla
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Aggiungi aeroporto
                </button>
            </div>
        </form>
    </div>
</div>
</main>
@include("footer")

<script>
    window.addEventListener('load', () => {
        const form = document.getElementById('airportForm');
        const cityInput = document.getElementById('city');
        const countryInput = document.getElementById('country');
        const nameInput = document.getElementById('name');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const cityError = document.getElementById('city-error');
        const countryError = document.getElementById('country-error');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Debounce helper
        let debounceTimer;
        function debounce(callback, delay = 500) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(callback, delay);
        }

        function resetFields() {
            nameInput.value = "";
            latInput.value = "";
            lonInput.value = "";
        }

        function showError(input, errorEl, message) {
            input.classList.add("is-invalid");
            errorEl.textContent = message;
        }

        function clearError(input, errorEl) {
            input.classList.remove("is-invalid");
            errorEl.textContent = "";
        }

        function validateLocation() {
            const city = cityInput.value.trim();
            const country = countryInput.value.trim();

            if (!city || !country) return;

            fetch("{{ route('admin.city.lookup') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ city, country })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        nameInput.value = "Aeroporto di " + capitalizeWords(city);
                        latInput.value = data.latitude;
                        lonInput.value = data.longitude;

                        clearError(cityInput, cityError);
                        clearError(countryInput, countryError);
                    } else {
                        resetFields();

                        showError(cityInput, cityError, data.message || "Città non valida");

                        if (data.invalid_country) {
                            showError(countryInput, countryError, "Paese non valido o non riconosciuto");
                        } else {
                            clearError(countryInput, countryError);
                        }
                    }
                })
                .catch(() => {
                    resetFields();
                    showError(cityInput, cityError, "Errore durante la verifica della città.");
                });
        }

        // Validazione prima dell’invio
        form.addEventListener('submit', (e) => {
            const city = cityInput.value.trim();
            const country = countryInput.value.trim();
            const lat = latInput.value;
            const lon = lonInput.value;

            clearError(cityInput, cityError);
            clearError(countryInput, countryError);

            if (!lat || !lon || !nameInput.value.includes(capitalizeWords(city.toLowerCase()))) {
                e.preventDefault();
                showError(cityInput, cityError, "Hai modificato la città dopo la verifica. Ricontrolla.");
                return;
            }

            // Previeni invii multipli
            submitBtn.disabled = true;
        });

        // Attiva validazione in modo controllato
        cityInput.addEventListener('blur', () => debounce(validateLocation));
        countryInput.addEventListener('blur', () => debounce(validateLocation));

        // Reset errori e valori su input
        cityInput.addEventListener('input', () => {
            resetFields();
            clearError(cityInput, cityError);
        });

        countryInput.addEventListener('input', () => {
            resetFields();
            clearError(countryInput, countryError);
        });
    });

    function capitalizeWords(str) {
        return str.replace(/\b\w/g, l => l.toUpperCase());
    }

    function initAutocomplete() {
        const countryInput = document.getElementById('country');
        const cityInput = document.getElementById('city');

        const countryAutocomplete = new google.maps.places.Autocomplete(countryInput, {
            types: ['(regions)'],
            componentRestrictions: { country: [] },  // Nessuna restrizione per avere più paesi
            fields: ['address_components', 'geometry'],
        });

        const cityAutocomplete = new google.maps.places.Autocomplete(cityInput, {
            types: ['(cities)'],
            componentRestrictions: { country: [] },
            fields: ['address_components', 'geometry'],
        });

        // Formatta l'input in italiano e con maiuscola
        countryAutocomplete.addListener('place_changed', () => {
            const place = countryAutocomplete.getPlace();
            if (place && place.address_components) {
                countryInput.value = capitalizeWords(
                    place.address_components[0].long_name.toLowerCase()
                );
            }
        });

        cityAutocomplete.addListener('place_changed', () => {
            const place = cityAutocomplete.getPlace();
            if (place && place.address_components) {
                cityInput.value = capitalizeWords(
                    place.address_components[0].long_name.toLowerCase()
                );
            }
        });
    }
</script>

<!-- Google Maps JavaScript API + Places -->
<script>
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",
        m=document,b=window;
        b=b[c]||(b[c]={});
        var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,
            u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));
                e.set("libraries",[...r]+"");
                for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);
                a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));
                a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)
            }));
        d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
        key: "{{ env('GOOGLE_MAPS_API') }}",
        v: "weekly",
        // Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
        // Add other bootstrap parameters as needed, using camel case.
    });
    google.maps.__ib__ = () => {
        google.maps.importLibrary("places").then(() => {
            initAutocomplete(); // ← ORA È SICURO CHIAMARLA
        });
    };

</script>
