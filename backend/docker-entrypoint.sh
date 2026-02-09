#!/bin/bash

# Exit on fail
set -e

echo "🚀 Starting deployment process..."

# 1. PAKSA HAPUS SEMUA CACHE LAMA (Wajib!)
# Ini akan menghapus file bootstrap/cache/config.php yang menyebabkan error
echo "🧹 Clearing ALL application cache..."
php artisan optimize:clear
php artisan config:clear

# 2. Re-cache (Opsional, tapi bagus untuk performa di production)
# Setelah bersih, baru kita cache ulang dengan config yang BARU dan BENAR
echo "⚡ Re-caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Migrasi & Seeding
echo "📦 Running Migrations..."
php artisan migrate --force

echo "🌱 Seeding Database..."
# Hapus --force di db:seed jika takut data duplikat, atau pastikan seeder aman
php artisan db:seed --force

# 4. Start Server
echo "🔥 Starting Server..."
exec "$@"
