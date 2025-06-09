# Dockerfile pour Symfony 7 + PHP 8.3
FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev libonig-dev libxml2-dev libzip-dev \
    libicu-dev libjpeg-dev libpng-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_pgsql intl zip opcache

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code source
WORKDIR /var/www/symfony
COPY . .

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/symfony

# Exposer le port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
