# ✈️ FlightTracker – Applicazione Web per il Monitoraggio di Voli in Tempo Reale

## 📘 Introduzione

**FlightTracker** è una piattaforma web pensata per il monitoraggio di voli in tempo reale. L’applicazione consente agli utenti di cercare voli, visualizzarne la posizione su mappa, ricevere notifiche sull’atterraggio e accedere a un'area personale per il tracciamento dei voli preferiti. Il sistema include anche una dashboard amministrativa per la gestione completa di aeroporti, voli e utenti.

---

## 🧠 Funzionalità principali

- Ricerca avanzata dei voli per città, compagnia o codice
- Visualizzazione dei voli su mappa dinamica aggiornata in tempo reale
- Area personale utente con tracciamento dei voli preferiti
- Notifiche push tramite WebSocket per segnalare l’atterraggio dei voli
- Pannello di controllo admin per la gestione di utenti, aeroporti e voli
- Filtri per visualizzare voli in partenza, in arrivo, atterrati o localizzati in Italia

---

## 🛠️ Tecnologie utilizzate

### Frontend
- HTML5, CSS3, JavaScript
- Bootstrap 5 per stile e layout responsive
- Blade (templating engine Laravel)

### Backend
- Laravel (PHP framework)
- WebSocket Server (Node.js)
- Laravel Scheduler per cron job

### Database
- MySQL con relazioni tra utenti, voli, aeroporti e modelli aerei

### Servizi Esterni
- Google Maps API (Places Autocomplete)
- WebSocket per notifiche push

---

## 🧱 Architettura WebSocket

- **websocket-server**: Server Node.js per invio notifiche
- **laravel-cron**: job Laravel schedulato ogni minuto per notificare atterraggi
- **frontend**: si connette tramite WebSocket e riceve notifiche in tempo reale

---

## 🔐 Funzionalità Admin

- Accesso tramite autenticazione
- Gestione utenti (modifica, rimozione)
- Gestione voli (aggiunta, modifica, eliminazione)
- Gestione aeroporti (autocomplete e validazione coordinate)
- Pannello organizzato con tabelle e schermate dedicate

---

## 👁️‍🗨️ Interfaccia Utente

- Home page con card animate e barra di ricerca dinamica
- Pagina dettagliata del volo con info su partenza, arrivo, posizione e modello
- Mappa personale dell’utente con voli seguiti
- Filtri interattivi per tipo di volo (partenza, arrivo, Italia, atterrati)

---

## 💬 Notifiche in tempo reale

- Sistema WebSocket centralizzato
- Integrazione con cron job per scansione voli atterrati
- Invio selettivo delle notifiche solo a utenti interessati
- Campo `notified` nella tabella pivot per evitare duplicati

---

