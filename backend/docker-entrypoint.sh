#!/bin/bash

# Exit on fail
set -e

# Cache konfigurasi untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Wiping and Reseeding Database..."
php artisan migrate:fresh --seed --force

# Jalankan command utama (Apache)
echo "Starting Apache..."
exec "$@"
