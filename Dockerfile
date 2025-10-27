FROM php:8.3-cli

WORKDIR /var/www

# Install system dependencies & extensions
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Create log file with proper permissions
RUN touch storage/logs/laravel.log && chmod 666 storage/logs/laravel.log

# Cache optimization
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Start server
CMD php artisan serve --host=0.0.0.0 --port=$PORT
