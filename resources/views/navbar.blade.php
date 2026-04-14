@include('user/notify_popup')
<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="ft-navbar {{ (auth()->check() && auth()->user()->is_admin && request()->routeIs('admin.*')) ? 'ft-navbar--light' : '' }}" aria-label="Navigazione principale">
    <div class="ft-navbar__inner">

        {{-- Brand --}}
        <a class="ft-navbar__brand" href="{{ route('home') }}" aria-label="FlightTracker – Home">
            <span class="ft-navbar__brand-accent">F</span>lightTracker
        </a>

        <ul class="ft-navbar__actions" role="list">
            @auth
                @if(auth()->user()->is_admin)
                    {{--
                        Admin: badge + link diretto alla dashboard + logout.
                        Stessa navbar su TUTTE le pagine (home, admin, ecc.).
                        Nessun dropdown: tooltip al hover per ogni azione.
                    --}}
                    <li>
                        <div class="ft-btn-group ft-btn-group--admin" role="group" aria-label="Strumenti amministratore">

                            <span class="ft-admin-badge" aria-label="Sei connesso come amministratore">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                     viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524z"/>
                                </svg>
                                <span class="ft-admin-badge-text">Admin</span>
                            </span>

                            <span class="ft-btn-group__sep" aria-hidden="true"></span>

                            <a class="ft-navbar__icon-btn" href="{{ route('admin.dashboard') }}"
                               data-tooltip="Pannello di controllo">
                                <img src="/images/admin_profile.png" alt="Profilo admin"
                                     class="ft-navbar__avatar ft-navbar__avatar--admin">
                            </a>

                            <span class="ft-btn-group__sep" aria-hidden="true"></span>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ft-navbar__icon-btn" data-tooltip="Esci">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M10 15a1 1 0 0 0 1-1v-4h-1v4H3V2h7v4h1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h7z"/>
                                        <path fill-rule="evenodd"
                                              d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>

                @else
                    {{-- Utente normale: mappa + profilo + logout, tutti link diretti con tooltip --}}
                    <li>
                        <div class="ft-btn-group" role="group" aria-label="Azioni account">

                            <a class="ft-navbar__icon-btn" href="{{ route('user.personal.map') }}"
                               data-tooltip="Mappa personale">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                     viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98 4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"/>
                                </svg>
                            </a>

                            <span class="ft-btn-group__sep" aria-hidden="true"></span>

                            <a class="ft-navbar__icon-btn" href="{{ route('user.profile') }}"
                               data-tooltip="Il mio profilo">
                                @if(auth()->user()->profile_picture_path)
                                    <img src="{{ route('profile.picture', ['filename' => auth()->user()->profile_picture_path]) }}"
                                         alt="Foto profilo" class="ft-navbar__avatar">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                    </svg>
                                @endif
                            </a>

                            <span class="ft-btn-group__sep" aria-hidden="true"></span>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ft-navbar__icon-btn" data-tooltip="Esci">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                              d="M10 15a1 1 0 0 0 1-1v-4h-1v4H3V2h7v4h1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h7z"/>
                                        <path fill-rule="evenodd"
                                              d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @endif
            @endauth

            @guest
                <li>
                    <div class="ft-btn-group" role="group" aria-label="Accesso account">
                        <a class="ft-navbar__link" href="{{ route('login.form') }}">Accedi</a>
                        <span class="ft-btn-group__sep" aria-hidden="true"></span>
                        <a class="ft-navbar__link" href="{{ route('register.form') }}">Registrati</a>
                    </div>
                </li>
            @endguest
        </ul>
    </div>

    <div class="ft-navbar__bar" aria-hidden="true"></div>
</nav>
