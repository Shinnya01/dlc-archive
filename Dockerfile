# Base PHP image
FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js and build assets (for Livewire, Vite, etc.)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs
RUN npm install && npm run build

# Set correct permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port for Laravel
EXPOSE 8000

# Start Laravel after the container boots
CMD php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8000
