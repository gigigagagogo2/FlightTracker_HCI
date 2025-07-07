{{-- Collegamento al CSS personalizzato della navbar --}}
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

<nav class="navbar navbar-light bg-light px-4">
    <div class="container-fluid d-flex justify-content-between">

        {{-- Menu di sinistra - solo icona Home --}}
        <ul class="navbar-nav d-flex flex-row">
            <li class="nav-item me-3">
                <a class="nav-link d-flex align-items-center" href="{{ route('home') }}" title="Home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="home-icon" viewBox="0 0 16 16" style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3)); transition: all 0.3s ease;">
                        <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
                    </svg>
                </a>
            </li>
        </ul>

        {{-- Menu di destra - link di autenticazione e profilo --}}
        <ul class="navbar-nav d-flex flex-row">
            @auth
                {{-- Admin vede area personale come utente, ma linka admin.dashboard --}}
                @if(auth()->user()->is_admin)
                    <li class="nav-item me-3">
                        <a class="nav-link d-flex align-items-center" href="{{ route('admin.dashboard') }}" title="Area personale">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profilo" class="profile-pic">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="profile-icon" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                </svg>
                            @endif
                        </a>
                    </li>
                @else
                    {{-- Utente normale --}}
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="{{ route('user.personal.map') }}" title="Mappa personale">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="map-icon" viewBox="0 0 16 16" style="filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3)); transition: all 0.3s ease;">
                                <defs>
                                    <linearGradient id="mapGradientUser" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:1" />
                                        <stop offset="50%" style="stop-color:#2196F3;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#FF9800;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#mapGradientUser)" fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98 4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"/>
                            </svg>
                        </a>
                    </li>

                    <li class="nav-item me-3">
                        <a class="nav-link d-flex align-items-center" href="{{ route('user.profile') }}" title="Area personale">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profilo" class="profile-pic">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="profile-icon" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                </svg>
                            @endif
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-decoration-none">Esci</button>
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
