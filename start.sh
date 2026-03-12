#!/bin/bash

set -e

# Ensure storage directories exist for static file serving
mkdir -p /var/www/storage/app/public /var/www/storage/framework/{cache,sessions,views}

# Attempt to keep writable permissions without failing startup
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Best-effort storage symlink for environments that expect it
php /var/www/artisan storage:link || true

# Start PHP-FPM
php-fpm &

# Start nginx in foreground
nginx -g 'daemon off;'
