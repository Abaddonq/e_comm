#!/bin/bash

# Start PHP-FPM
php-fpm &

# Start nginx in foreground
nginx -g 'daemon off;'
