FROM php:8.2-fpm-alpine

# Installa le dipendenze di sistema necessarie per Laravel e Composer
RUN apk update && apk add --no-cache \
    libzip-dev \
    curl \
    bash \
    mysql-client && \
    rm -rf /var/cache/apk/*  # Rimuove la cache di apk

# Installa Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installa le estensioni PHP necessarie per Laravel
RUN docker-php-ext-configure zip && \
    docker-php-ext-install zip pdo pdo_mysql

# Copia lo script di attesa per MySQL
COPY ./wait-for-mysql.sh /usr/local/bin/wait-for-mysql.sh
RUN chmod +x /usr/local/bin/wait-for-mysql.sh

# Imposta la directory di lavoro
WORKDIR /var/www/html

# Copia solo i file necessari prima di installare le dipendenze
COPY composer.json composer.lock ./

# Copia il resto dei file del progetto
COPY . .

# Installa le dipendenze di Laravel (incluso Composer)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Esponi la porta su cui il server Laravel girerà
EXPOSE 8000

# Comando per eseguire Laravel, con attesa per MySQL
CMD /usr/local/bin/wait-for-mysql.sh && php artisan serve --host=0.0.0.0 --port=8000

