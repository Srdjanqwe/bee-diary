# ---------- Stage 1: build frontend assets (Vue + Vite) ----------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ---------- Stage 2: install Composer (PHP) dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs

# ---------- Stage 3: final runtime image ----------
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        gettext \
        postgresql-dev \
        libzip-dev \
        oniguruma-dev \
        curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && rm -rf /var/cache/apk/*

WORKDIR /var/www/html

# App code
COPY . .

# Vendor from Composer stage, built assets from Node stage
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Now that full app code + vendor are in place, finish autoload
RUN composer dump-autoload --optimize --no-dev 2>/dev/null || true

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
