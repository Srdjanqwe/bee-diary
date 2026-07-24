#!/usr/bin/env bash
set -e

# Render injects $PORT at runtime; fallback for local docker testing
export PORT="${PORT:-10000}"

# Substitute ONLY $PORT - leave nginx's own $uri, $document_root etc. untouched
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Run pending migrations on every deploy (safe to leave on; remove if you prefer manual control)
php artisan migrate --force || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
