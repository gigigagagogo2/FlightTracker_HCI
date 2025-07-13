<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/main-content.css') }}">
    <title>Area Personale</title>
    <link rel="stylesheet" href="{{ asset('css/user/personal_area.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


</head>
<body>
@include("navbar")
<main class="main-content">

    <div class="profile-card">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        @endif

        @error('profile_picture')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
        @enderror

        @error('nickname')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
        @enderror

        @error('email')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
        @enderror

        @error('password')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
        </div>
        @enderror

        <!-- FORM 1: solo immagine -->
        <form id="pictureForm" action="{{ route('user.updatePicture') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="profile_picture" class="profile-image-wrapper">
                <div class="profile-image-circle">
                    <img
                        src="{{ $user->profile_picture_path
                            ? route('profile.picture', ['filename' => $user->profile_picture_path])
                            : asset('images/default_user.jpg')
                        }}"
                        alt="Foto profilo"
                    >
                    <span class="edit-icon">
                    <i class="bi bi-pencil"></i>
                    </span>
                </div>
            </label>
            <input type="file" id="profile_picture" name="profile_picture"
                   onchange="document.getElementById('pictureForm').submit();">
        </form>

        <!-- FORM 2: info profilo -->
        <form id="profileForm" action="{{ route('user.updateProfile') }}" method="POST">
            @csrf

            <div class="profile-info">
                <input type="text" name="nickname" value="{{ Auth::user()->nickname }}" class="form-input" readonly>
                <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-input" readonly>
                <input type="password" name="password" value="********" class="form-input" readonly>
            </div>

            <button type="button" class="edit-btn" onclick="enableEdit()">Modifica profilo</button>
            <button type="button" class="cancel-btn" style="display: none;" onclick="cancelEdit()">Annulla</button>
            <button type="submit" class="save-btn" style="display: none;">Salva modifiche</button>
        </form>

    </div>
</main>

@include("footer")
<script>
    function enableEdit() {
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => input.removeAttribute('readonly'));
        document.querySelector('.edit-btn').style.display = 'none';
        document.querySelector('.save-btn').style.display = 'inline-block';
        document.querySelector('.cancel-btn').style.display = 'inline-block';
    }

    function cancelEdit() {
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.setAttribute('readonly', true);
            input.value = input.defaultValue;
        });
        document.querySelector('.edit-btn').style.display = 'inline-block';
        document.querySelector('.save-btn').style.display = 'none';
        document.querySelector('.cancel-btn').style.display = 'none';
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            // Dopo 5 secondi (3000 ms), nasconde con effetto fade
            setTimeout(() => {
                // Aggiunge la classe Bootstrap 'fade' e poi rimuove l'elemento
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500); // rimuove fisicamente dopo la transizione
            }, 3000);
        });
    });
</script>
</body>
</html>
