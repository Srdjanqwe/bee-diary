# ---------- Stage 1: build frontend assets (Vue + Vite) ----------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ---------- Stage 2: final runtime image (PHP + composer + nginx) ----------
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        gettext \
        git \
        unzip \
        postgresql-dev \
        postgresql-client \
        libzip-dev \
        oniguruma-dev \
        curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath \
    && rm -rf /var/cache/apk/*

# Bring in the composer binary (no separate stage/copy of vendor - simpler & more reliable)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Full app code first (composer needs it for package auto-discovery scripts)
COPY . .

# Built frontend assets from stage 1
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --no-scripts --optimize-autoloader --ignore-platform-reqs

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
