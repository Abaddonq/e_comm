FROM php:8.2-cli

# Install system dependencies and Node.js
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev gnupg && \
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

RUN composer install --no-dev --optimize-autoloader

# Build Frontend Assets
RUN npm install && npm run build

# Make startup script executable
RUN chmod +x /var/www/start-railway.sh

RUN mkdir -p /var/www/storage/app/public /var/www/storage/uploads /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["/var/www/start-railway.sh"]
