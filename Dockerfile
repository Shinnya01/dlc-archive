# Use PHP 8.2 CLI as base
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies without running any artisan commands
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install Node.js and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs
RUN npm install && npm run build

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose Laravel port
EXPOSE 8000

# Run everything after the container starts (runtime)
CMD php artisan config:clear && php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8000
