#!/usr/bin/env bash
set -euo pipefail

echo "==> Attente de la base de données (${DB_HOST:-db}:${DB_PORT:-3306})"
for i in $(seq 1 30); do
    if php -r "exit(@fsockopen(getenv('DB_HOST') ?: 'db', (int) (getenv('DB_PORT') ?: 3306)) ? 0 : 1);"; then
        echo "==> Base de données disponible"
        break
    fi
    echo "==> DB indisponible, nouvelle tentative dans 2s ($i/30)"
    sleep 2
done

echo "==> Exécution des migrations"
php artisan migrate --force

echo "==> Démarrage d'Apache"
exec apache2-foreground
