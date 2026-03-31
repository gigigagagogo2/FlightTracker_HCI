FROM php:8.2-fpm-alpine

# Installa le dipendenze di sistema necessarie per Laravel e Composer
RUN apk update && apk add --no-cache \
  libzip-dev \
  curl \
  git \
  bash \
  mysql-client \
  oniguruma-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  zip \
  unzip \
  openssl-dev \
  ca-certificates && \
  rm -rf /var/cache/apk/*

# Installa le estensioni PHP necessarie per Laravel
RUN docker-php-ext-configure zip && \
  docker-php-ext-install zip pdo pdo_mysql
# Installa Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Imposta la directory di lavoro
WORKDIR /var/www/html

# Copia tutto tranne i file indicati in `.dockerignore`
COPY . .

# Copia lo script di avvio
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Espone la porta su cui Laravel serve (se usi `php artisan serve`)
EXPOSE 8000

# Comando di avvio
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
