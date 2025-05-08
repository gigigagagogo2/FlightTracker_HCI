<nav class="navbar navbar-light bg-light px-4">
    <div class="container-fluid d-flex justify-content-between">
        <ul class="navbar-nav d-flex flex-row">
            <li class="nav-item me-3">
                <a class="nav-link" href="{{ route('home') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Altro</a>
            </li>
        </ul>

        <ul class="navbar-nav d-flex flex-row">
            @auth
                @if(auth()->user()->is_admin)
                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Area personale</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.personal.map') }}">Mappa personale</a>
                    </li>

                    <li class="nav-item me-3">
                        <a class="nav-link" href="{{ route('user.profile') }}">Area personale</a>
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
