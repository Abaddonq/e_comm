#!/bin/bash

# Exit on error
set -e

echo "Starting Railway Deployment Environment Setup..."

# Ensure we are in the correct directory
cd /var/www

# Ensure storage directory exists and is writable
mkdir -p storage/framework/{cache,sessions,views}
chmod -R 775 storage bootstrap/cache

# Clear and rebuild caches for production
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
echo "Creating storage symlink..."
php artisan storage:link || true

# Start the application
# Using artisan serve for now as per current Dockerfile, 
# but consider PHP-FPM/Nginx for true production.
echo "Starting application server..."
php artisan serve --host=0.0.0.0 --port=9000
