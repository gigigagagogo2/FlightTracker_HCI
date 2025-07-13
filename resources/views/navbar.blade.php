@include('user/notify_popup')
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<!-- Bootstrap Bundle JS (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-light bg-light px-4">
    <div class="container-fluid d-flex justify-content-between">

        {{-- Menu di sinistra - logo + testo FlightTracker --}}
        <ul class="navbar-nav d-flex flex-row align-items-center">
            <li class="nav-item me-3 d-flex align-items-center">
                <a class="nav-link d-flex align-items-center" href="{{ route('home') }}" title="Home">
                    <span style="font-weight: 700; font-size: 1.1rem; color: #333;">FlightTracker</span>
                </a>
            </li>
        </ul>

        {{-- Menu di destra - About Us + autenticazione e profilo --}}
        <ul class="navbar-nav d-flex flex-row align-items-center">

            <li class="nav-item me-4">
                <a class="nav-link" href="{{ route('about') }}" title="Chi siamo">Chi siamo</a>
            </li>

            @auth
                @if(auth()->user()->is_admin)
                    <li class="nav-item me-3">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.dashboard') }}"
                           title="Area personale">
                            <img src="/images/admin_profile.png" alt="Profilo" class="profile-pic"
                                 style="width: 24px; height: 24px;">
                        </a>
                    </li>
                @else
                    <li class="nav-item me-3">
                        <a class="nav-link d-flex align-items-center" href="{{ route('user.personal.map') }}"
                           title="Mappa personale">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="map-icon"
                                 viewBox="0 0 16 16"
                                 style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3)); transition: all 0.3s ease; margin-right: 6px;">
                                <defs>
                                    <linearGradient id="mapGradientUser" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:1"/>
                                        <stop offset="50%" style="stop-color:#2196F3;stop-opacity:1"/>
                                        <stop offset="100%" style="stop-color:#FF9800;stop-opacity:1"/>
                                    </linearGradient>
                                </defs>
                                <path fill="url(#mapGradientUser)" fill-rule="evenodd"
                                      d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98 4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"/>
                            </svg>
                        </a>
                    </li>

                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle-no-caret d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            @if(auth()->user()->profile_picture_path)
                                <img
                                    src="{{ route('profile.picture', ['filename' => auth()->user()->profile_picture_path]) }}"
                                    alt="Profilo" class="profile-pic rounded-circle" style="width: 24px; height: 24px;">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                     class="profile-icon" viewBox="0 0 16 16">
                                    <path
                                        d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                </svg>
                            @endif
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('user.profile') }}">Profilo</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.flights') }}">I miei voli</a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-decoration-none" title="Logout"
                                style="padding: 0; border: none; background: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                 class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                      d="M10 15a1 1 0 0 0 1-1v-4h-1v4H3V2h7v4h1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h7z"/>
                                <path fill-rule="evenodd"
                                      d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                            </svg>
                        </button>
                    </form>
                </li>
            @endauth

            @guest
                <li class="nav-item me-3">
                    <a class="nav-link" href="{{ route('login.form') }}">Accedi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register.form') }}">Registrati</a>
                </li>
            @endguest
        </ul>
    </div>
</nav>
