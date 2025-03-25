# Utiliser une image PHP avec les extensions requises
FROM php:8.2-fpm

# Installer les dépendances pour PHP et Composer
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_mysql

# Installer Node.js et NPM (version 16.x)
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers Laravel dans le conteneur
COPY . .

# Installer les dépendances Laravel avec Composer
RUN composer install --no-dev --optimize-autoloader

# Installer les dépendances frontend avec NPM
RUN npm install

# Lancer `npm run dev` pour démarrer les services frontend (comme les notifications)
RUN npm run dev

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port PHP pour PHP-FPM
EXPOSE 9000