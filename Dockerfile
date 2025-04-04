FROM php:8.2-fpm

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    netcat-openbsd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP avec zip
RUN docker-php-ext-configure zip \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Installation de Node.js et npm (version 20.x)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers du projet
COPY . /var/www


RUN npm install
# Installation des dépendances Composer
RUN composer install --no-interaction --no-scripts --no-progress --prefer-dist

# Installation des dépendances npm


# Générer la clé d'application Laravel
RUN php artisan key:generate

# Nettoyer le cache de configuration
RUN php artisan config:clear

# Permissions
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache
# Définir les permissions pour les répertoires storage et bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN npm install


EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]