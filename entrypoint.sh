#!/bin/sh

# Impostiamo PMA_HOST a "db" se non è già definito
: ${PMA_HOST:="db"}

# Aspetta che MySQL sia pronto
until nc -z -v -w30 "$PMA_HOST" 3306; do
  echo "Waiting for MySQL at $PMA_HOST:3306..."
  sleep 1
done
echo "MySQL is up - continuing"

# Copia .env.example in .env se non esiste
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Installa dipendenze se mancano
if [ ! -f vendor/autoload.php ]; then
  echo "Eseguo composer install..."
  composer install --no-dev --optimize-autoloader
fi

# Genera la APP_KEY se non ancora settata
if ! grep -q "APP_KEY=base64:" .env; then
  php artisan key:generate
fi

# Controlla se il database è già migrato (es. verifica se la tabella 'migrations' esiste)
echo "Controllo stato del database..."
if ! php artisan migrate:status > /dev/null 2>&1; then
    echo "Database non inizializzato. Eseguo migrate e seed..."
    php artisan migrate --seed
else
    echo "Database già migrato. Salto migrate/seed."
fi

# Avvia il server Laravel o il cron scheduler in base a CONTAINER_TYPE
if [ "$CONTAINER_TYPE" = "cron" ]; then
    echo "Starting Laravel scheduler..."
    while true; do
        php artisan schedule:run
        sleep 60
    done
else
    php artisan serve --host=0.0.0.0 --port=8000
fi
