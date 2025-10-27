FROM php:8.2-cli

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Create log file
RUN touch storage/logs/laravel.log && chmod 666 storage/logs/laravel.log

# Generate APP_KEY if not set
RUN if [ -z "$APP_KEY" ]; then php artisan key:generate; fi

# Cache config
RUN php artisan config:cache

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=$PORT
