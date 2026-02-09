#!/bin/bash

# Exit on fail
set -e

# --- DEBUGGING: Cek apakah variable terbaca ---
if [ -z "$CLOUDINARY_URL" ]; then
    echo "⚠️  WARNING: CLOUDINARY_URL is NOT set or empty!"
else
    echo "✅ CLOUDINARY_URL found (starts with: ${CLOUDINARY_URL:0:15}...)"
fi

# --- MATIKAN INI SEMENTARA ---
# php artisan config:cache
# (Kita matikan config:cache dulu agar Laravel membaca langsung env dari Render.
# Jika error hilang, berarti masalahnya ada di timing caching).

# Cache route & view boleh tetap nyala
php artisan route:cache
php artisan view:cache

# --- PERUBAHAN PENTING (Sesuai scriptmu) ---
echo "Running Migrations..."
php artisan migrate --force

echo "Seeding Admin..."
php artisan db:seed --force

echo "Starting Apache..."
exec "$@"
