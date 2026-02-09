#!/bin/bash

# Exit on fail
set -e

# Cache konfigurasi untuk performa
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- PERUBAHAN PENTING ---
# Ganti 'migrate:fresh' (hapus data) menjadi 'migrate' (tambah data baru saja)
echo "Running Migrations..."
php artisan migrate --force

# Jalankan Seeder (Aman, karena di DatabaseSeeder kita sudah pakai 'if exists' untuk admin)
echo "Seeding Admin..."
php artisan db:seed --force

# Jalankan command utama (Apache)
echo "Starting Apache..."
exec "$@"
