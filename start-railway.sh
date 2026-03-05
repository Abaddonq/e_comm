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
# Use PHP built-in server with a custom router so we can
# apply cache headers and conditional responses for static assets.
echo "Starting application server..."
php -S 0.0.0.0:9000 -t public server-router.php
