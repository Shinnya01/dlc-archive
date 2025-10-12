# ============================================
# Stage 1: Build Composer dependencies
# ============================================
FROM composer:2.7 AS composer_builder

WORKDIR /app

# Copy composer files and install dependencies (no dev)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# ============================================
# Stage 2: Build frontend assets (Vite or Mix)
# ============================================
FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package.json package-lock.json* yarn.lock* ./
RUN npm install

COPY . .
RUN npm run build

# ============================================
# Stage 3: Final PHP + Nginx image
# ============================================
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache nginx bash git supervisor curl zip unzip libpng-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Create app directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy built vendor and assets from previous stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public/build ./public/build

# Create storage symlink
RUN php artisan storage:link || true

# Copy nginx configuration
COPY .docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor configuration
COPY .docker/supervisord.conf /etc/supervisord.conf

# Set permissions for Laravel storage and bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Set environment variables (for DigitalOcean)
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=${APP_URL:-http://localhost}

# Expose the HTTP port
EXPOSE 8080

# Start both PHP-FPM and Nginx via Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
