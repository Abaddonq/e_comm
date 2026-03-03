FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nginx

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
WORKDIR /var/www
COPY . /var/www

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN mkdir -p /var/www/storage/uploads /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views
RUN chmod -R 755 /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY nginx_default.conf /etc/nginx/sites-available/default

# Run both services using a simple script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD /start.sh
