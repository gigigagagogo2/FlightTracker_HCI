@php use Illuminate\Support\Str; @endphp
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/it.js"></script>
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
        <p class="sidebar-title" style="cursor: pointer;" onclick="goToWelcome()">
            Pannello di Controllo
        </p>
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
                    <tbody id="tbody-users"></tbody>
                </table>
            </div>
            <div id="pagination-users"></div>

        </div>

        <!-- SEZIONE VOLI -->
        <div class="content-section" id="section-flights">
            <div class="section-header">
                <h2><i class="bi bi-airplane-engines text-success"></i> Voli registrati</h2>
                <button type="button" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                        onclick="openCreateFlightModal()">
                    <i class="bi bi-plus-circle"></i> Aggiungi volo
                </button>
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
                    <tbody id="tbody-flights"></tbody>
                </table>
            </div>
            <div id="pagination-flights"></div>

        </div>

        <!-- SEZIONE AEROPORTI -->
        <div class="content-section" id="section-airports">
            <div class="section-header">
                <h2><i class="fas fa-building text-warning"></i> Aeroporti registrati</h2>
                <button type="button" class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1 text-white"
                        onclick="openCreateAirportModal()">
                    <i class="bi bi-plus-circle"></i> Aggiungi aeroporto
                </button>
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
                    <tbody id="tbody-airports"></tbody>
                </table>
            </div>
            <div id="pagination-airports"></div>
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

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

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

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>


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

                    <div class="d-flex justify-content-end gap-2 pt-3 ">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
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
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Stai per eliminare l'utente:</p>

                <div class="text-start mb-3 px-2" style="font-size: 0.9rem; background: #f8fafc; border-radius: 0.6rem; padding: 0.75rem 1rem;">
                    <p class="mb-0">
                        <i class="bi bi-person-fill text-primary me-1"></i>
                        <span class="text-muted">Nickname:</span> <strong id="delete_user_name"></strong>
                    </p>
                </div>

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

<!-- MODAL CREA VOLO -->
<div class="modal fade" id="createFlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #10b981, #059669); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 0.6rem; padding: 0.4rem 0.5rem;">
                        <i class="bi bi-airplane-engines fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Aggiungi Volo</h5>
                        <small style="opacity: 0.8;">Inserisci i dati del nuovo volo</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.75rem;">
                <form id="createFlightForm" method="POST" action="{{ route('admin.flights.store') }}">
                    @csrf
                    <input type="hidden" name="_form" value="create_flight">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeromobile</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-airplane text-muted"></i>
                            </span>
                            <select name="airplane_model_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona il modello dell'aeromobile</option>
                                @foreach ($airplaneModels as $model)
                                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('airplane_model_id', 'createFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeroporto di partenza </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="departure_airport_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona l'aeroporto di partenza</option>
                                @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->city }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('departure_airport_id', 'createFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeroporto di arrivo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="arrival_airport_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona l'aeroporto di arrivo</option>
                                @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->city }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('arrival_airport_id', 'createFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Orario di partenza</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                    <i class="bi bi-clock text-muted"></i>
                                </span>
                                <input type="text" name="departure_time" id="departure_time_picker"
                                       class="form-control border-start-0 flatpickr-input"
                                       value="{{ old('departure_time') }}"
                                       placeholder="Seleziona data e ora"
                                       style="border-radius: 0 0.6rem 0.6rem 0;" required readonly>
                            </div>
                            @error('departure_time', 'createFlight')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Orario di arrivo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                    <i class="bi bi-clock text-muted"></i>
                                </span>
                                <input type="text" name="arrival_time" id="arrival_time_picker"
                                       class="form-control border-start-0 flatpickr-input"
                                       value="{{ old('arrival_time') }}"
                                       placeholder="Seleziona data e ora"
                                       style="border-radius: 0 0.6rem 0.6rem 0;" required readonly>
                            </div>
                        </div>
                        @error('arrival_time', 'createFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 ">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
                        </button>
                        <button type="submit" class="btn px-4"
                                style="border-radius: 0.6rem; font-weight: 500; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi volo
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- MODAL EDIT VOLO -->
<div class="modal fade" id="editFlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #10b981, #059669); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 0.6rem; padding: 0.4rem 0.5rem;">
                        <i class="bi bi-airplane-engines fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Modifica Volo</h5>
                        <small style="opacity: 0.8;">Aggiorna i dati del volo</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.75rem;">
                <form id="editFlightForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_flight_id" name="flight_id">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeromobile</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-airplane text-muted"></i>
                            </span>
                            <select name="airplane_model_id" id="edit_airplane_model_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona il modello dell'aeromobile</option>
                                @foreach ($airplaneModels as $model)
                                    <option value="{{ $model->id }}">{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('airplane_model_id', 'editFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeroporto di partenza</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="departure_airport_id" id="edit_departure_airport_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona l'aeroporto di partenza</option>
                                @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->city }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('departure_airport_id', 'editFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Aeroporto di arrivo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <select name="arrival_airport_id" id="edit_arrival_airport_id" class="form-select border-start-0" style="border-radius: 0 0.6rem 0.6rem 0;" required>
                                <option value="">Seleziona l'aeroporto di arrivo</option>
                                @foreach ($airports as $airport)
                                    <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->city }})</option>
                                @endforeach
                            </select>
                        </div>
                        @error('arrival_airport_id', 'editFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Orario di parteza</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                    <i class="bi bi-clock text-muted"></i>
                                </span>
                                <input type="text" name="departure_time" id="edit_departure_time_picker"
                                       class="form-control border-start-0 flatpickr-input"
                                       placeholder="Seleziona data e ora"
                                       style="border-radius: 0 0.6rem 0.6rem 0;" required readonly>
                            </div>
                            @error('departure_time', 'editFlight')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Orario di arrivo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                    <i class="bi bi-clock text-muted"></i>
                                </span>
                                <input type="text" name="arrival_time" id="edit_arrival_time_picker"
                                       class="form-control border-start-0 flatpickr-input"
                                       placeholder="Seleziona data e ora"
                                       style="border-radius: 0 0.6rem 0.6rem 0;" required readonly>
                            </div>

                        </div>
                        @error('arrival_time', 'editFlight')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
                        </button>
                        <button type="submit" class="btn px-4"
                                style="border-radius: 0.6rem; font-weight: 500; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none;">
                            <i class="bi bi-pencil-square me-1"></i>Salva modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DELETE VOLO -->
<div class="modal fade" id="deleteFlightModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-body text-center" style="padding: 2rem 1.75rem;">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2;">
                    <i class="bi bi-trash3-fill" style="font-size: 1.75rem; color: #ef4444;"></i>
                </div>

                <h5 class="fw-bold mb-1">Elimina volo</h5>
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Stai per eliminare il volo:</p>

                <div class="text-start mb-3 px-2" style="font-size: 0.9rem; background: #f8fafc; border-radius: 0.6rem; padding: 0.75rem 1rem;">
                    <p class="mb-1">
                        <i class="bi bi-airplane-fill text-success me-1"></i>
                        <span class="text-muted">Partenza da</span> <strong id="delete_flight_departure"></strong>
                        <span class="text-muted ms-1">alle</span> <strong id="delete_flight_departure_time"></strong>
                    </p>
                    <p class="mb-0">
                        <i class="bi bi-airplane-fill text-success me-1"></i>
                        <span class="text-muted">In arrivo a</span> <strong id="delete_flight_arrival"></strong>
                        <span class="text-muted ms-1">alle</span> <strong id="delete_flight_arrival_time"></strong>
                    </p>
                </div>

                <p class="text-muted" style="font-size: 0.82rem;">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    Questa azione è irreversibile.
                </p>

                <form id="deleteFlightForm" method="POST">
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

<!-- MODAL CREA AEROPORTO -->
<div class="modal fade" id="createAirportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 0.6rem; padding: 0.4rem 0.5rem;">
                        <i class="fas fa-building fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Aggiungi Aeroporto</h5>
                        <small style="opacity: 0.8;">Inserisci i dati del nuovo aeroporto</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.75rem;">
                <form id="createAirportForm" method="POST" action="{{ route('admin.airport.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Paese</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-globe text-muted"></i>
                            </span>
                            <input id="country" name="country" type="text"
                                   class="form-control border-start-0"
                                   placeholder="Es. Italia"
                                   autocomplete="off" required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                        <div id="country-error" class="text-danger small mt-1"></div>
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>


                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Città</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-geo-alt text-muted"></i>
                            </span>
                            <input type="text" id="city" name="city"
                                   class="form-control border-start-0"
                                   placeholder="Es. Milano"
                                   autocomplete="off" required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                        <div id="city-error" class="text-danger small mt-1"></div>
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>


                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Nome aeroporto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <input type="text" id="airport_name" name="name"
                                   class="form-control border-start-0"
                                   placeholder="Es. Malpensa"
                                   required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
                        </button>
                        <button type="submit" id="createAirportSubmit" class="btn px-4"
                                style="border-radius: 0.7rem; font-weight: 600; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                            <i class="bi bi-plus-circle me-1"></i>Aggiungi aeroporto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT AEROPORTO -->
<div class="modal fade" id="editAirportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">

            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 1.5rem 1.75rem;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 0.6rem; padding: 0.4rem 0.5rem;">
                        <i class="fas fa-building fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Modifica Aeroporto</h5>
                        <small style="opacity: 0.8;">Aggiorna i dati dell'aeroporto</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="padding: 1.75rem;">
                <form id="editAirportForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Paese</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-globe text-muted"></i>
                            </span>
                            <input id="edit_country" name="country" type="text"
                                   class="form-control border-start-0"
                                   placeholder="Es. Italia"
                                   autocomplete="off" required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                        <div id="edit_country_error" class="text-danger small mt-1"></div>
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Città</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="bi bi-geo-alt text-muted"></i>
                            </span>
                            <input type="text" id="edit_city" name="city"
                                   class="form-control border-start-0"
                                   placeholder="Es. Milano"
                                   autocomplete="off" required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                        <div id="edit_city_error" class="text-danger small mt-1"></div>
                    </div>

                    <div style="height: 1px; background: #f1f5f9; margin: 0.75rem 0 1rem 0;"></div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Nome aeroporto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 0.6rem 0 0 0.6rem;">
                                <i class="fas fa-building text-muted"></i>
                            </span>
                            <input type="text" id="edit_airport_name" name="name"
                                   class="form-control border-start-0"
                                   placeholder="Es. Malpensa"
                                   required
                                   style="border-radius: 0 0.6rem 0.6rem 0;">
                        </div>
                    </div>

                    <input type="hidden" name="latitude" id="edit_latitude">
                    <input type="hidden" name="longitude" id="edit_longitude">

                    <div class="d-flex justify-content-end gap-2 pt-2">
                        <button type="button" class="btn px-4" data-bs-dismiss="modal"
                                style="border-radius: 0.7rem; font-weight: 600; background: #f1f5f9; color: #475569; border: none;">
                            Annulla
                        </button>
                        <button type="submit" id="editAirportSubmit" class="btn px-4"
                                style="border-radius: 0.7rem; font-weight: 600; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none;">
                            <i class="bi bi-pencil-square me-1"></i>Salva modifiche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DELETE AEROPORTO -->
<div class="modal fade" id="deleteAirportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">
            <div class="modal-body text-center" style="padding: 2rem 1.75rem;">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width: 64px; height: 64px; border-radius: 50%; background: #fef2f2;">
                    <i class="bi bi-trash3-fill" style="font-size: 1.75rem; color: #ef4444;"></i>
                </div>
                <h5 class="fw-bold mb-1">Elimina aeroporto</h5>
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Stai per eliminare:</p>

                <div class="text-start mb-3 px-2" style="font-size: 0.9rem; background: #f8fafc; border-radius: 0.6rem; padding: 0.75rem 1rem;">
                    <p class="mb-0">
                        <i class="fas fa-building me-1"></i>
                        <strong id="delete_airport_name"></strong>
                    </p>
                </div>
                <p class="text-muted" style="font-size: 0.82rem;">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    Questa azione è irreversibile.
                </p>
                <form id="deleteAirportForm" method="POST">
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

        // Ripristina la sezione attiva dopo redirect o refresh
        const hash = window.location.hash.replace('#', '');
        const savedSection = sessionStorage.getItem('adminSection');
        const activeSection = hash || savedSection;

        if (activeSection && document.getElementById('section-' + activeSection)) {
        document.querySelectorAll('.nav-item-admin').forEach(b => b.classList.remove('active'));
        document.querySelector(`[data-section="${activeSection}"]`).classList.add('active');
        welcome.style.display = 'none';
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + activeSection).classList.add('active');
        history.replaceState(null, '', window.location.pathname);
        loadSectionData(activeSection);
    }

        // Navigazione sidebar
        navItems.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.section;
            navItems.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            welcome.style.display = 'none';
            sections.forEach(s => s.classList.remove('active'));
            document.getElementById('section-' + target).classList.add('active');
            sessionStorage.setItem('adminSection', target);
            loadSectionData(target);
        });
    });

        function goToWelcome() {
        navItems.forEach(b => b.classList.remove('active'));
        sections.forEach(s => s.classList.remove('active'));
        welcome.style.display = '';
        sessionStorage.removeItem('adminSection');
        history.replaceState(null, '', window.location.pathname);
    }

        // Attiva tooltip Bootstrap
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

        // Auto-dismiss toast dopo 3 secondi
        document.querySelectorAll('.toast').forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    });

        // Stato paginazione per ogni sezione
        const pagination = { users: 1, flights: 1, airports: 1 };

        function loadSectionData(section, page = 1) {
            const endpoints = {
                users:    '/admin/users/data',
                flights:  '/admin/flights/data',
                airports: '/admin/airports/data',
            };

            const tbody = document.getElementById(`tbody-${section}`);
            if (!tbody) return;

            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-4">
        <div class="spinner-border spinner-border-sm me-2"></div>Caricamento...
    </td></tr>`;

            fetch(`${endpoints[section]}?page=${page}`)
                .then(res => res.json())
                .then(response => {
                    const data = response.data ?? response.data;
                    const items = Array.isArray(response) ? response : (response.data || []);
                    const currentPage = response.current_page ?? 1;
                    const lastPage = response.last_page ?? 1;
                    const total = response.total ?? items.length;

                    tbody.innerHTML = renderRows(section, items);
                    tbody.dataset.loaded = 'true';
                    pagination[section] = currentPage;

                    // Aggiorna paginazione
                    updatePagination(section, currentPage, lastPage, total);

                    tbody.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        new bootstrap.Tooltip(el);
                    });
                })
                .catch(() => {
                    tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-4">Errore nel caricamento dei dati.</td></tr>`;
                });
        }

        function updatePagination(section, currentPage, lastPage, total) {
            const container = document.getElementById(`pagination-${section}`);
            if (!container) return;

            // Colore attivo per sezione
            const colors = {
                users:    '#3b82f6',
                flights:  '#10b981',
                airports: '#f59e0b',
            };
            const activeColor = colors[section] ?? '#64748b';

            // Genera i numeri di pagina con ellissi
            let pages = [];
            if (lastPage <= 7) {
                for (let i = 1; i <= lastPage; i++) pages.push(i);
            } else {
                pages.push(1);
                if (currentPage > 3) pages.push('...');
                for (let i = Math.max(2, currentPage - 1); i <= Math.min(lastPage - 1, currentPage + 1); i++) {
                    pages.push(i);
                }
                if (currentPage < lastPage - 2) pages.push('...');
                pages.push(lastPage);
            }

            const pageButtons = pages.map(p => {
                if (p === '...') return `<span class="px-2 text-muted" style="line-height: 2rem;">…</span>`;
                const isActive = p === currentPage;
                return `<button class="btn btn-sm"
            style="border-radius: 0.5rem; min-width: 2rem;
                   background: ${isActive ? activeColor : '#f1f5f9'};
                   color: ${isActive ? 'white' : '#475569'};
                   border: ${isActive ? `2px solid ${activeColor}` : 'none'};"
            ${isActive ? 'disabled' : `onclick="loadSectionData('${section}', ${p})"`}>
            ${p}
        </button>`;
            }).join('');

            container.innerHTML = `
        <div class="d-flex align-items-center justify-content-center gap-1 px-3 py-2">
            <button class="btn btn-sm"
                style="border-radius: 0.5rem; background: #f1f5f9; color: #475569; border: none;"
                ${currentPage <= 1 ? 'disabled' : ''}
                onclick="loadSectionData('${section}', ${currentPage - 1})">
                <i class="bi bi-chevron-left"></i>
            </button>
            ${pageButtons}
            <button class="btn btn-sm"
                style="border-radius: 0.5rem; background: #f1f5f9; color: #475569; border: none;"
                ${currentPage >= lastPage ? 'disabled' : ''}
                onclick="loadSectionData('${section}', ${currentPage + 1})">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    `;
        }

        function reloadSection(section) {
            const tbody = document.getElementById(`tbody-${section}`);
            if (tbody) delete tbody.dataset.loaded;
            loadSectionData(section, pagination[section] ?? 1);
        }

        function renderRows(section, data) {
        if (section === 'users') {
        return data.map(u => `
                <tr>
                    <td>${u.id}</td>
                    <td>${u.nickname}</td>
                    <td>${u.email}</td>
                    <td>Utente</td>
                    <td>
                        <div class="action-group">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Modifica utente"
                                onclick="openEditUserModal(${u.id}, '${u.nickname}', '${u.email}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <span class="action-separator"></span>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Elimina utente"
                                onclick="openDeleteUserModal(${u.id}, '${u.nickname}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
    }

        if (section === 'flights') {
        return data.map(f => `
                <tr>
                    <td>${f.id}</td>
                    <td>${f.airplane_model}</td>
                    <td>${f.departure_airport}</td>
                    <td>${f.arrival_airport}</td>
                    <td>${f.departure_time}</td>
                    <td>${f.arrival_time}</td>
                    <td>
                        <div class="action-group">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Modifica volo"
                                onclick="openEditFlightModal(${f.id}, ${f.airplane_model_id}, ${f.departure_airport_id}, ${f.arrival_airport_id}, '${f.departure_time}', '${f.arrival_time}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <span class="action-separator"></span>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Elimina volo"
                                onclick="openDeleteFlightModal(${f.id}, '${f.departure_airport}', '${f.departure_time}', '${f.arrival_airport}', '${f.arrival_time}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
    }

        if (section === 'airports') {
        return data.map(a => `
                <tr>
                    <td>${a.id}</td>
                    <td>${a.name ?? '—'}</td>
                    <td>${a.city ?? '—'}</td>
                    <td>${a.country ?? '—'}</td>
                    <td>${a.latitude ?? '–'}</td>
                    <td>${a.longitude ?? '–'}</td>
                    <td>
                        <div class="action-group">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Modifica aeroporto"
                                onclick="openEditAirportModal(${a.id}, '${a.country}', '${a.city}', '${a.name}', '${a.latitude}', '${a.longitude}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <span class="action-separator"></span>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Elimina aeroporto"
                                onclick="openDeleteAirportModal(${a.id}, '${a.name}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`).join('');
    }
    }

        // ── UTENTI ──
        function openEditUserModal(id, nickname, email) {
        document.getElementById('modal_nickname').value = nickname;
        document.getElementById('modal_nickname').placeholder = nickname;
        document.getElementById('modal_email').value = email;
        document.getElementById('modal_email').placeholder = email;
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

        // ── VOLI ──
        function openCreateFlightModal() {
        new bootstrap.Modal(document.getElementById('createFlightModal')).show();
    }

        @if ($errors->hasBag('createFlight'))
        document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('createFlightModal')).show();
    });
        @endif

        @if (!$errors->hasBag('createFlight'))
        document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#createFlightForm select').forEach(select => {
            select.selectedIndex = 0;
        });
        const dep = document.getElementById('departure_time_picker');
        const arr = document.getElementById('arrival_time_picker');
        if (dep) dep.value = '';
        if (arr) arr.value = '';
    });
        @endif

        document.getElementById('createFlightModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('createFlightForm').reset();
        document.getElementById('departure_time_picker')._flatpickr.clear();
        document.getElementById('arrival_time_picker')._flatpickr.clear();
        document.querySelectorAll('#createFlightForm select').forEach(select => {
        select.selectedIndex = 0;
    });
        document.querySelectorAll('#createFlightModal .text-danger').forEach(el => el.textContent = '');
    });

        flatpickr("#departure_time_picker", {
        locale: "it", enableTime: true, dateFormat: "d/m/Y H:i",
        time_24hr: true, minuteIncrement: 1, monthSelectorType: "static",
        @if(old('departure_time')) defaultDate: "{{ old('departure_time') }}", @endif
    });

        flatpickr("#arrival_time_picker", {
        locale: "it", enableTime: true, dateFormat: "d/m/Y H:i",
        time_24hr: true, minuteIncrement: 1, monthSelectorType: "static",
        @if(old('arrival_time')) defaultDate: "{{ old('arrival_time') }}", @endif
    });

        const editDeparturePicker = flatpickr("#edit_departure_time_picker", {
        locale: "it", enableTime: true, dateFormat: "d/m/Y H:i",
        time_24hr: true, minuteIncrement: 1, monthSelectorType: "static",
    });

        const editArrivalPicker = flatpickr("#edit_arrival_time_picker", {
        locale: "it", enableTime: true, dateFormat: "d/m/Y H:i",
        time_24hr: true, minuteIncrement: 1, monthSelectorType: "static",
    });

        function openEditFlightModal(id, airplaneModelId, departureAirportId, arrivalAirportId, departureTime, arrivalTime) {
        document.getElementById('editFlightForm').action = `/admin/flights/${id}`;
        document.getElementById('edit_airplane_model_id').value = airplaneModelId;
        document.getElementById('edit_departure_airport_id').value = departureAirportId;
        document.getElementById('edit_arrival_airport_id').value = arrivalAirportId;
        document.getElementById('edit_flight_id').value = id;
        editDeparturePicker.setDate(departureTime);
        editArrivalPicker.setDate(arrivalTime);
        new bootstrap.Modal(document.getElementById('editFlightModal')).show();
    }

        @if ($errors->hasBag('editFlight'))
        document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editFlightForm').action = `/admin/flights/{{ old('flight_id') }}`;
        editDeparturePicker.setDate("{{ old('departure_time') }}");
        editArrivalPicker.setDate("{{ old('arrival_time') }}");
        new bootstrap.Modal(document.getElementById('editFlightModal')).show();
    });
        @endif

        document.getElementById('editFlightModal').addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('#editFlightModal .text-danger').forEach(el => el.textContent = '');
    });

        function openDeleteFlightModal(id, departure, departureTime, arrival, arrivalTime) {
        document.getElementById('delete_flight_departure').textContent = departure;
        document.getElementById('delete_flight_departure_time').textContent = departureTime;
        document.getElementById('delete_flight_arrival').textContent = arrival;
        document.getElementById('delete_flight_arrival_time').textContent = arrivalTime;
        document.getElementById('deleteFlightForm').action = `/admin/flights/${id}`;
        new bootstrap.Modal(document.getElementById('deleteFlightModal')).show();
    }

        // ── AEROPORTI ──
        function openCreateAirportModal() {
        new bootstrap.Modal(document.getElementById('createAirportModal')).show();
    }

        document.getElementById('createAirportModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('createAirportForm').reset();
        document.getElementById('city-error').textContent = '';
        document.getElementById('country-error').textContent = '';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('city').classList.remove('is-invalid');
        document.getElementById('country').classList.remove('is-invalid');
    });

        (function() {
        const cityInput = document.getElementById('city');
        const countryInput = document.getElementById('country');
        const nameInput = document.getElementById('airport_name');
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');
        const cityError = document.getElementById('city-error');
        const countryError = document.getElementById('country-error');
        const submitBtn = document.getElementById('createAirportSubmit');

        let debounceTimer;
        function debounce(callback, delay = 500) { clearTimeout(debounceTimer); debounceTimer = setTimeout(callback, delay); }
        function resetFields() { nameInput.value = ""; latInput.value = ""; lonInput.value = ""; }
        function showError(input, errorEl, message) { input.classList.add("is-invalid"); errorEl.textContent = message; }
        function clearError(input, errorEl) { input.classList.remove("is-invalid"); errorEl.textContent = ""; }

        function validateLocation() {
        const city = cityInput.value.trim();
        const country = countryInput.value.trim();
        if (!city || !country) return;
        fetch("{{ route('admin.city.lookup') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value },
        body: JSON.stringify({ city, country })
    })
        .then(res => res.json())
        .then(data => {
        if (data.success) {
        latInput.value = data.latitude; lonInput.value = data.longitude;
        nameInput.value = ""; nameInput.focus();
        clearError(cityInput, cityError); clearError(countryInput, countryError);
    } else {
        resetFields();
        showError(cityInput, cityError, data.message || "Città non valida");
        if (data.invalid_country) showError(countryInput, countryError, "Paese non valido o non riconosciuto");
        else clearError(countryInput, countryError);
    }
    })
        .catch(() => { resetFields(); showError(cityInput, cityError, "Errore durante la verifica della città."); });
    }

        document.getElementById('createAirportForm').addEventListener('submit', (e) => {
        clearError(cityInput, cityError); clearError(countryInput, countryError);
        if (!latInput.value || !lonInput.value) {
        e.preventDefault();
        showError(cityInput, cityError, "Hai modificato la città o il paese dopo la verifica. Ricontrolla.");
        return;
    }
        submitBtn.disabled = true;
    });

        cityInput.addEventListener('blur', () => debounce(validateLocation));
        countryInput.addEventListener('blur', () => debounce(validateLocation));
        cityInput.addEventListener('input', () => { resetFields(); clearError(cityInput, cityError); });
        countryInput.addEventListener('input', () => { resetFields(); clearError(countryInput, countryError); });
    })();

        function openEditAirportModal(id, country, city, name, latitude, longitude) {
        document.getElementById('edit_country').value = country;
        document.getElementById('edit_city').value = city;
        document.getElementById('edit_airport_name').value = name;
        document.getElementById('edit_latitude').value = latitude;
        document.getElementById('edit_longitude').value = longitude;
        document.getElementById('editAirportForm').action = `/admin/airports/${id}`;
        document.getElementById('edit_country_error').textContent = '';
        document.getElementById('edit_city_error').textContent = '';
        document.getElementById('edit_city').classList.remove('is-invalid');
        document.getElementById('edit_country').classList.remove('is-invalid');
        new bootstrap.Modal(document.getElementById('editAirportModal')).show();
    }

        document.getElementById('editAirportModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('edit_country_error').textContent = '';
        document.getElementById('edit_city_error').textContent = '';
        document.getElementById('edit_city').classList.remove('is-invalid');
        document.getElementById('edit_country').classList.remove('is-invalid');
    });

        (function() {
        const cityInput = document.getElementById('edit_city');
        const countryInput = document.getElementById('edit_country');
        const latInput = document.getElementById('edit_latitude');
        const lonInput = document.getElementById('edit_longitude');
        const cityError = document.getElementById('edit_city_error');
        const countryError = document.getElementById('edit_country_error');
        const submitBtn = document.getElementById('editAirportSubmit');

        let debounceTimer;
        function debounce(callback, delay = 500) { clearTimeout(debounceTimer); debounceTimer = setTimeout(callback, delay); }
        function resetFields() { latInput.value = ""; lonInput.value = ""; }
        function showError(input, errorEl, message) { input.classList.add("is-invalid"); errorEl.textContent = message; }
        function clearError(input, errorEl) { input.classList.remove("is-invalid"); errorEl.textContent = ""; }

        function validateLocation() {
        const city = cityInput.value.trim();
        const country = countryInput.value.trim();
        if (!city || !country) return;
        fetch("{{ route('admin.city.lookup') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value },
        body: JSON.stringify({ city, country })
    })
        .then(res => res.json())
        .then(data => {
        if (data.success) {
        latInput.value = data.latitude; lonInput.value = data.longitude;
        clearError(cityInput, cityError); clearError(countryInput, countryError);
    } else {
        resetFields();
        showError(cityInput, cityError, data.message || "Città non valida");
        if (data.invalid_country) showError(countryInput, countryError, "Paese non valido o non riconosciuto");
        else clearError(countryInput, countryError);
    }
    })
        .catch(() => { resetFields(); showError(cityInput, cityError, "Errore durante la verifica della città."); });
    }

        document.getElementById('editAirportForm').addEventListener('submit', (e) => {
        clearError(cityInput, cityError); clearError(countryInput, countryError);
        if (!latInput.value || !lonInput.value) {
        e.preventDefault();
        showError(cityInput, cityError, "Hai modificato la città o il paese dopo la verifica. Ricontrolla.");
        return;
    }
        submitBtn.disabled = true;
    });

        cityInput.addEventListener('blur', () => debounce(validateLocation));
        countryInput.addEventListener('blur', () => debounce(validateLocation));
        cityInput.addEventListener('input', () => { resetFields(); clearError(cityInput, cityError); });
        countryInput.addEventListener('input', () => { resetFields(); clearError(countryInput, countryError); });
    })();

        function openDeleteAirportModal(id, name) {
        document.getElementById('delete_airport_name').textContent = name;
        document.getElementById('deleteAirportForm').action = `/admin/airports/${id}`;
        new bootstrap.Modal(document.getElementById('deleteAirportModal')).show();
    }

</script>
<script>
    function capitalizeWords(str) {
        return str.replace(/\b\w/g, l => l.toUpperCase());
    }

    function initAutocomplete() {
        const countryInput = document.getElementById('country');
        const cityInput = document.getElementById('city');

        const countryAutocomplete = new google.maps.places.Autocomplete(countryInput, {
            types: ['(regions)'], fields: ['address_components'],
        });
        countryAutocomplete.addListener('place_changed', () => {
            const place = countryAutocomplete.getPlace();
            if (place?.address_components) {
                const c = place.address_components.find(c => c.types.includes('country'));
                if (c) countryInput.value = capitalizeWords(c.long_name.toLowerCase());
            }
        });

        const cityAutocomplete = new google.maps.places.Autocomplete(cityInput, {
            types: ['(cities)'], fields: ['address_components'],
        });
        cityAutocomplete.addListener('place_changed', () => {
            const place = cityAutocomplete.getPlace();
            if (place?.address_components) {
                const c = place.address_components.find(c =>
                    c.types.includes('locality') ||
                    c.types.includes('administrative_area_level_2') ||
                    c.types.includes('administrative_area_level_1')
                );
                if (c) cityInput.value = capitalizeWords(c.long_name.toLowerCase());
            }
        });
    }
</script>

<script>
    (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",
        m=document,b=window;b=b[c]||(b[c]={});
        var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,
            u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));
                e.set("libraries",[...r]+"");
                for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));
                a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));
        d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
        key: "{{ env('GOOGLE_MAPS_API') }}", v: "weekly",
    });
    google.maps.__ib__ = () => {
        google.maps.importLibrary("places").then(() => { initAutocomplete(); });
    };
</script>
</body>
</html>
