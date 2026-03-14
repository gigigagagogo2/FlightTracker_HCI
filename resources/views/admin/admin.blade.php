<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
</head>
<body>

@include('navbar')
<!-- TOAST FEEDBACK -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">

    @if(session('success'))
        <div id="toastSuccess" class="toast align-items-center text-white border-0 show"
             role="alert" style="background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 0.75rem;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="toastError" class="toast align-items-center text-white border-0 show"
             role="alert" style="background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 0.75rem;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

</div>

<!-- LAYOUT A DUE PANNELLI -->
<div class="admin-layout">

    <!-- ── SIDEBAR ── -->
    <aside class="admin-sidebar">
        <p class="sidebar-title">Pannello di Controllo</p>

        <button class="nav-item-admin" data-section="users">
            <i class="bi bi-people-fill nav-icon"></i>
            Gestisci Utenti
        </button>

        <button class="nav-item-admin" data-section="flights">
            <i class="bi bi-airplane-engines nav-icon"></i>
            Gestisci Voli
        </button>

        <button class="nav-item-admin" data-section="airports">
            <i class="fas fa-building nav-icon"></i>
            Gestisci Aeroporti
        </button>
    </aside>

    <!-- ── MAIN ── -->
    <main class="admin-main">

        <!-- Stato iniziale: nessuna selezione -->
        <div class="welcome-panel" id="welcome">
            <i class="bi bi-grid-3x3-gap"></i>
            <h4>Seleziona una sezione</h4>
            <p>Scegli una voce dal menu a sinistra per gestire utenti, voli o aeroporti.</p>
        </div>

        <!-- SEZIONE UTENTI -->
        <div class="content-section" id="section-users">
            <div class="section-header">
                <h2><i class="bi bi-people-fill text-primary"></i> Utenti registrati</h2>
            </div>
            <div class="table-placeholder">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nickname</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th>Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        @if (!$user->is_admin)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->nickname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>Utente</td>
                                <td>
                                    <div class="action-group">
                                        <button type="button"
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="tooltip" title="Modifica utente"
                                                onclick="openEditUserModal({{ $user->id }}, '{{ $user->nickname }}', '{{ $user->email }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <span class="action-separator"></span>

                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip" title="Elimina utente"
                                                onclick="openDeleteUserModal({{ $user->id }}, '{{ $user->nickname }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SEZIONE VOLI -->
        <div class="content-section" id="section-flights">
            <div class="section-header">
                <h2><i class="bi bi-airplane-engines text-success"></i> Voli registrati</h2>
                <a href="{{ route('admin.flights.create') }}" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-circle"></i> Aggiungi volo
                </a>
            </div>
            <div class="table-placeholder">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modello Aereo</th>
                        <th>Aeroporto Partenza</th>
                        <th>Aeroporto Arrivo</th>
                        <th>Partenza</th>
                        <th>Arrivo</th>
                        <th>Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($flights as $flight)
                        <tr>
                            <td>{{ $flight->id }}</td>
                            <td>{{ $flight->airplaneModel->name ?? '—' }}</td>
                            <td>{{ $flight->departureAirport->name ?? '—' }}</td>
                            <td>{{ $flight->arrivalAirport->name ?? '—' }}</td>
                            <td>{{ $flight->departure_time }}</td>
                            <td>{{ $flight->arrival_time ?? '–' }}</td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.flights.edit', $flight->id) }}"
                                       class="btn btn-sm btn-warning"
                                       data-bs-toggle="tooltip" title="Modifica volo">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <span class="action-separator"></span>

                                    <form action="{{ route('admin.flights.delete', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip" title="Elimina volo"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo volo?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SEZIONE AEROPORTI -->
        <div class="content-section" id="section-airports">
            <div class="section-header">
                <h2><i class="fas fa-building text-warning"></i> Aeroporti registrati</h2>
                <a href="{{ route('admin.airport.create') }}" class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1 text-white">
                    <i class="bi bi-plus-circle"></i> Aggiungi aeroporto
                </a>
            </div>
            <div class="table-placeholder">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome aeroporto</th>
                        <th>Città</th>
                        <th>Paese</th>
                        <th>Latitudine</th>
                        <th>Longitudine</th>
                        <th>Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($airports as $airport)
                        <tr>
                            <td>{{ $airport->id }}</td>
                            <td>{{ $airport->name ?? '—' }}</td>
                            <td>{{ $airport->city ?? '—' }}</td>
                            <td>{{ $airport->country ?? '—' }}</td>
                            <td>{{ $airport->latitude ?? '–' }}</td>
                            <td>{{ $airport->longitude ?? '–' }}</td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('admin.airport.edit', $airport->id) }}"
                                       class="btn btn-sm btn-warning"
                                       data-bs-toggle="tooltip" title="Modifica aeroporto">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <span class="action-separator"></span>

                                    <form action="{{ route('admin.airport.delete', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                data-bs-toggle="tooltip" title="Elimina aeroporto"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo aeroporto?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- MODAL EDIT UTENTE -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <!-- Header colorato -->
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 0.6rem; padding: 0.4rem 0.5rem;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editUserModalLabel">Modifica Utente</h5>
                        <small style="opacity: 0.8;" id="modal_user_subtitle">Aggiorna i dati dell'utente</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.75rem;">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="modal_nickname" class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Nickname</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-at text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-1" id="modal_nickname" name="nickname"
                                   placeholder="Es. mario_rossi"
                                   style="border-radius: 0 0.6rem 0.6rem 0;" required>
                        </div>
                        <div class="text-danger small mt-1" id="modal_nickname_error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_email" class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0 ps-1" id="modal_email" name="email"
                                   placeholder="Es. mario@esempio.it"
                                   style="border-radius: 0 0.6rem 0.6rem 0;" required>
                        </div>
                        <div class="text-danger small mt-1" id="modal_email_error"></div>
                    </div>

                    <div class="mb-4">
                        <label for="modal_password" class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Nuova Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0 ps-1" id="modal_password" name="password"
                                   placeholder="Lascia vuoto per non cambiarla"
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.6rem; font-weight: 500; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none;">
                            <i class="bi bi-x-lg me-1"></i>Annulla
                        </button>
                        <button type="submit" class="btn px-4"
                                style="border-radius: 0.6rem; font-weight: 500; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none;">
                            <i class="bi bi-pencil-square me-1"></i>Salva modifiche
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- MODAL DELETE UTENTE -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-body text-center" style="padding: 2rem 1.75rem;">
                <!-- Icona grande -->
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2;">
                    <i class="bi bi-trash3-fill" style="font-size: 1.75rem; color: #ef4444;"></i>
                </div>

                <h5 class="fw-bold mb-1">Elimina utente</h5>
                <p class="text-muted mb-1" style="font-size: 0.9rem;">Stai per eliminare l'utente:</p>
                <p class="fw-bold mb-3" style="font-size: 1.1rem; color: #1e293b;" id="delete_user_name"></p>
                <p class="text-muted" style="font-size: 0.82rem;">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    Questa azione è irreversibile.
                </p>

                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn flex-fill py-2" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
                        </button>
                        <button type="submit" class="btn flex-fill py-2"
                                style="border-radius: 0.7rem; font-weight: 600; background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none;">
                            <i class="bi bi-trash me-1"></i>Elimina
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    const navItems = document.querySelectorAll('.nav-item-admin');
    const sections = document.querySelectorAll('.content-section');
    const welcome  = document.getElementById('welcome');

    navItems.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.section;
            navItems.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            welcome.style.display = 'none';
            sections.forEach(s => s.classList.remove('active'));
            document.getElementById('section-' + target).classList.add('active');
        });
    });

    // Attiva tooltip Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // Auto-dismiss toast dopo 3 secondi
    document.querySelectorAll('.toast').forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    });

    // Ripristina la sezione attiva dopo redirect
    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('section-' + hash)) {
        document.querySelectorAll('.nav-item-admin').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-section="${hash}"]`).classList.add('active');
        document.getElementById('welcome').style.display = 'none';
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + hash).classList.add('active');

        // Rimuove il #hash dall'URL senza ricaricare la pagina
        history.replaceState(null, '', window.location.pathname);
    }

    function openEditUserModal(id, nickname, email) {
        document.getElementById('modal_nickname').value = nickname;
        document.getElementById('modal_email').value = email;
        document.getElementById('modal_password').value = '';
        document.getElementById('editUserForm').action = `/admin/users/${id}`;
        document.getElementById('modal_nickname_error').textContent = '';
        document.getElementById('modal_email_error').textContent = '';
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function openDeleteUserModal(id, nickname) {
        document.getElementById('delete_user_name').textContent = nickname;
        document.getElementById('deleteUserForm').action = `/admin/users/${id}`;
        new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
    }
</script>

</body>
</html>
