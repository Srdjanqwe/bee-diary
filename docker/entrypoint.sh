#!/usr/bin/env bash
set -e

# Render injects $PORT at runtime; fallback for local docker testing
export PORT="${PORT:-10000}"

echo ">>> Rendering nginx config for PORT=$PORT"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo ">>> Running composer package discovery"
php artisan package:discover --ansi || echo ">>> WARNING: package:discover failed, continuing anyway"

echo ">>> Running migrations"
php artisan migrate --force || echo ">>> WARNING: migrate failed, continuing anyway"

echo ">>> Checking if database needs seeding"
USER_COUNT=$(PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "$DB_USERNAME" -d "$DB_DATABASE" -tAc "SELECT COUNT(*) FROM users;" 2>/dev/null | tr -d '[:space:]')

if [ "$USER_COUNT" = "0" ]; then
    echo ">>> Users table is empty, running db:seed"
    php artisan db:seed --force || echo ">>> WARNING: db:seed failed, continuing anyway"
else
    echo ">>> Users already exist ($USER_COUNT), skipping seed"
fi

echo ">>> Caching config"
php artisan config:cache || echo ">>> WARNING: config:cache failed, continuing anyway"

echo ">>> Caching routes"
php artisan route:cache || echo ">>> WARNING: route:cache failed, continuing anyway"

echo ">>> Caching views"
php artisan view:cache || echo ">>> WARNING: view:cache failed, continuing anyway"

echo ">>> Starting nginx + php-fpm"
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
