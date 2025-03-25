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
    nodejs \
    npm \
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

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www

# Copie des fichiers du projet
COPY . /var/www

# Installer les dépendances Composer sans interaction
RUN composer install --no-interaction --no-scripts --no-progress --prefer-dist

# Installer les dépendances npm
RUN npm install

# Générer la clé d'application Laravel
RUN php artisan key:generate

# Permissions
RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]