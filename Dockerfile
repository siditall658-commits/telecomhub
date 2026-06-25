FROM php:8.2-apache

# 1. Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Installer les extensions PHP requises par Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Installer Node.js pour pouvoir compiler les assets avec Vite
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 4. Activer le module rewrite d'Apache (indispensable pour les routes Laravel)
RUN a2enmod rewrite

# 5. Configurer le dossier public d'Apache comme racine du site
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 6. Installer Composer globalement
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 7. Copier les fichiers du projet dans le conteneur
WORKDIR /var/www/html
COPY . /var/www/html

# 8. Installer les dépendances PHP sans exécuter de scripts (évite les blocages au build)
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# 9. Installer les dépendances CSS/JS et compiler avec Vite pour la production
RUN npm install
RUN npm run build

# 10. Donner les permissions correctes pour le stockage et les caches
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Préparer le script de déploiement
COPY deploy.sh /usr/local/bin/deploy.sh
RUN chmod +x /usr/local/bin/deploy.sh

EXPOSE 80

# Lancer le script au démarrage du conteneur
CMD ["deploy.sh"]