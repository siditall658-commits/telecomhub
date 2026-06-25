#!/bin/sh

# 1. Forcer la création du dossier et du fichier SQLite s'ils n'existent pas
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite

# 2. Donner les droits totaux à la base de données
chmod -R 777 /var/www/html/database

# 3. Forcer Composer à reconstruire la carte des classes (résout le problème "Class not found")
composer dump-autoload --optimize --no-scripts

# 4. Vider et régénérer tous les caches Laravel pour la production
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Executer automatiquement les migrations de la base de données
php artisan migrate --force

# 6. Configurer Apache pour qu'il lise le fichier .htaccess de Laravel
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# 7. Démarrer le serveur Apache en premier plan
apache2-foreground