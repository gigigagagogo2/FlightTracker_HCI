<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
    <!-- FORM 1: solo immagine -->
    <form id="pictureForm" action="{{ route('user.updatePicture') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="profile_picture" class="profile-image-wrapper">
            <div class="profile-image-circle">
                <!-- ??: Se la variabile a sinistra non e' nulla restituisce il suo valore -->
                <img src="{{ asset(Auth::user()->profile_picture_path ?? 'images/default_user.jpg') }}" alt="Foto profilo">
                <span class="edit-icon">
                    <i class="bi bi-pencil"></i>
                </span>

            </div>
        </label>
        <input type="file" id="profile_picture" name="profile_picture" onchange="document.getElementById('pictureForm').submit();">
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

<!-- TODO: includere footer -->
@include("footer")
@include('user/notify_popup')
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

</body>
</html>
