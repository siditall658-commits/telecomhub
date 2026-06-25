#!/bin/sh

# 1. Forcer la création du dossier et du fichier SQLite s'ils n'existent pas
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chmod -R 777 /var/www/html/database

# 2. Forcer Composer à reconstruire la carte des classes
composer dump-autoload --optimize --no-scripts

# 3. Vider et mettre en cache la configuration
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Lancer automatiquement les migrations pour créer les tables
php artisan migrate --force

# 5. Démarrer le serveur Apache
apache2-foreground