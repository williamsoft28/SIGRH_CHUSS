FROM php:8.4-apache

# Installer les dépendances système requises par Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Activer le module rewrite d'Apache
RUN a2enmod rewrite

# Installer les extensions PHP nécessaires (incluant PostgreSQL et MySQL)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql pgsql zip

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . .

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer les dépendances PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Installer les dépendances Node et compiler les assets (Vite)
RUN npm install && npm run build

# Modifier le DocumentRoot d'Apache pour pointer vers le dossier public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Créer l'architecture complète des dossiers storage au cas où ils manquent
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Ajuster les permissions pour Laravel (777 permet d'être 100% sûr qu'Apache peut écrire)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache


# Exposer le port 80 (Render redirigera le trafic externe vers ce port)
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
