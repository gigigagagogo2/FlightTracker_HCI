<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Personale</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Aggiungi il file CSS separato -->
    <link href="{{ asset('css/personal_area.css') }}" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Scheda Utente -->
    <div class="profile-card position-relative">
        <!-- Immagine del profilo -->
        <div>
            <img id="profilePic" src="{{ auth()->user()->profile_picture ?? '/images/default-avatar.jpg' }}" alt="Immagine Profilo">
            <i class="fas fa-pencil-alt edit-icon" id="editIcon"></i>
        </div>

        <!-- Info utente -->
        <div class="profile-info">
            <h5>{{ auth()->user()->name }}</h5>
            <p>{{ auth()->user()->email }}</p>
        </div>
    </div>

    <!-- Modal per modificare l'immagine del profilo -->
    <div class="modal" id="editProfilePicModal" tabindex="-1" aria-labelledby="editProfilePicModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfilePicModalLabel">Cambia Immagine del Profilo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="profilePicForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profilePicInput" class="form-label">Seleziona immagine</label>
                            <input type="file" class="form-control" id="profilePicInput" name="profile_picture" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Salva</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Quando l'utente clicca sull'icona della matita, mostra il modal per caricare una nuova foto
    document.getElementById('editIcon').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('editProfilePicModal'));
        modal.show();
    });

    // Quando l'utente invia il form per caricare la nuova immagine
    document.getElementById('profilePicForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Impedisce il comportamento predefinito del form

        const formData = new FormData(this);

        // Invia l'immagine al server tramite AJAX
        fetch("{{ route('user.updatePicture') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna l'immagine del profilo sulla pagina senza ricaricare
                    document.getElementById('profilePic').src = data.imageUrl;
                    alert("Immagine del profilo aggiornata!");
                } else {
                    alert("Errore durante l'aggiornamento dell'immagine.");
                }
            })
            .catch(error => {
                console.error("Errore:", error);
                alert("Errore durante il caricamento dell'immagine.");
            });
    });
</script>
</body>
</html>
