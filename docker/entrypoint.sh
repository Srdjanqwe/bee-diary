#!/usr/bin/env bash
set -e

# Render injects $PORT at runtime; fallback for local docker testing
export PORT="${PORT:-10000}"

echo ">>> Rendering nginx config for PORT=$PORT"
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo ">>> Running migrations"
php artisan migrate --force || echo ">>> WARNING: migrate failed, continuing anyway"

echo ">>> Caching config"
php artisan config:cache || echo ">>> WARNING: config:cache failed, continuing anyway"

echo ">>> Caching routes"
php artisan route:cache || echo ">>> WARNING: route:cache failed, continuing anyway"

echo ">>> Caching views"
php artisan view:cache || echo ">>> WARNING: view:cache failed, continuing anyway"

echo ">>> Starting nginx + php-fpm"
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
