FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
WORKDIR /var/www
COPY . /var/www

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy nginx config
COPY default.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 80

# Run supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
