#!/bin/sh

# Vider et mettre en cache la configuration
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarrer le serveur Apache en premier plan (obligatoire pour Docker)
apache2-foreground