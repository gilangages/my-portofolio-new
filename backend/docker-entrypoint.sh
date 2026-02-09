#!/bin/bash

# Exit on fail
set -e

# Cache konfigurasi untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan migrasi database (Force karena production)
php artisan migrate --force

# Jalankan Seeder (Otomatis buat Admin jika belum ada)
echo "Running Seeders..."
php artisan db:seed --force

# Jalankan command utama (Apache)
exec "$@"
