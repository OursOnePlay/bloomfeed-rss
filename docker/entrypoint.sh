#!/usr/bin/env bash
set -euo pipefail

echo "==> Waiting for the database (${DB_HOST:-db}:${DB_PORT:-3306})"
for i in $(seq 1 30); do
    if php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'db', (int) (getenv('DB_PORT') ?: 3306)) ? 0 : 1);"; then
        echo "==> Database available"
        break
    fi
    echo "==> Database not available yet, retrying in 2s ($i/30)"
    sleep 2
done

echo "==> Running migrations"
php artisan migrate --force

echo "==> Starting Apache"
exec apache2-foreground
