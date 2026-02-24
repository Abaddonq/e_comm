# Deployment Guide - Shared Hosting

## Overview

This guide provides step-by-step instructions for deploying the DecoreMotto E-Commerce platform to shared hosting environments (specifically Hostinger Business Plan or similar).

## Prerequisites

- Shared hosting account with:
  - PHP 8.2 or higher
  - MySQL 5.7 or higher
  - SSH/SFTP access
  - Ability to set cron jobs
  - At least 1GB storage space

## Deployment Steps

### 1. Prepare Application for Production

On your local machine:

```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Build production assets
npm run build

# Clear development caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 2. Configure Production Environment

Create a production `.env` file:

```env
# ==============================================
# APPLICATION SETTINGS (PRODUCTION)
# ==============================================
APP_NAME="DecoreMotto E-Commerce"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# ==============================================
# DATABASE CONFIGURATION
# ==============================================
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

# ==============================================
# CACHE & QUEUE (Shared Hosting)
# ==============================================
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# ==============================================
# SESSION CONFIGURATION (PRODUCTION)
# ==============================================
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# ==============================================
# MAIL CONFIGURATION
# ==============================================
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================
# PAYMENT GATEWAY (PRODUCTION)
# ==============================================
PAYMENT_GATEWAY=iyzico

# iyzico Production
IYZICO_API_KEY=your_production_api_key
IYZICO_SECRET_KEY=your_production_secret_key
IYZICO_BASE_URL=https://api.iyzipay.com

# OR Stripe Production
STRIPE_API_KEY=your_production_api_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret

# ==============================================
# ADMIN PANEL SECURITY
# ==============================================
ADMIN_ROUTE_PREFIX=your-unique-secure-prefix-xyz789

# ==============================================
# LOGGING
# ==============================================
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### 3. Upload Files to Shared Hosting

#### Option A: Using SFTP/FTP

1. Connect to your hosting via SFTP (recommended) or FTP
2. Upload the entire Laravel project to a directory **outside** public_html (e.g., `/home/username/laravel/`)
3. Exclude these directories/files from upload:
   - `node_modules/`
   - `.git/`
   - `tests/`
   - `storage/logs/*` (but keep the directory structure)
   - `.env` (upload separately with production values)

#### Option B: Using SSH and Git

```bash
# SSH into your hosting
ssh username@yourdomain.com

# Navigate to your home directory
cd ~

# Clone the repository
git clone your-repository-url laravel

# Navigate to project
cd laravel

# Install dependencies
composer install --no-dev --optimize-autoloader
```

### 4. Configure Web Root (public_html)

You have two options:

#### Option A: Symlink Method (Recommended if supported)

```bash
# Remove default public_html
rm -rf ~/public_html

# Create symlink to Laravel public directory
ln -s ~/laravel/public ~/public_html
```

#### Option B: Copy Method

```bash
# Copy contents of public directory to public_html
cp -r ~/laravel/public/* ~/public_html/

# Modify public_html/index.php
# Change these lines:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

# To:
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

### 5. Setup Database

Using cPanel or phpMyAdmin:

1. Create a new MySQL database
2. Create a database user with all privileges
3. Note the database name, username, and password
4. Update `.env` file with these credentials

Run migrations via SSH:

```bash
cd ~/laravel
php artisan migrate --force
php artisan db:seed --force
```

### 6. Set File Permissions

```bash
cd ~/laravel

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set storage and cache permissions
chmod -R 775 storage bootstrap/cache
```

### 7. Create Storage Symlink

```bash
cd ~/laravel
php artisan storage:link
```

If this fails, manually create the symlink:

```bash
ln -s ~/laravel/storage/app/public ~/public_html/storage
```

### 8. Optimize Application

```bash
cd ~/laravel

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 9. Setup Cron Jobs

Add this cron job via cPanel (runs every minute):

```bash
* * * * * cd /home/username/laravel && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

For weekly cleanup of failed jobs:

```bash
0 0 * * 0 cd /home/username/laravel && php artisan queue:prune-failed >> /dev/null 2>&1
```

### 10. Configure SSL Certificate

Using cPanel:

1. Go to SSL/TLS section
2. Install Let's Encrypt SSL certificate (usually free)
3. Force HTTPS by adding to `.htaccess` in public_html:

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 11. Verify Deployment

Test these URLs:

- Homepage: `https://yourdomain.com`
- Admin Panel: `https://yourdomain.com/your-admin-prefix/dashboard`
- Test registration and login
- Test product browsing
- Test cart functionality
- Test checkout flow (use test payment mode first)

## Post-Deployment Checklist

- [ ] APP_DEBUG is set to `false`
- [ ] APP_ENV is set to `production`
- [ ] Database credentials are correct
- [ ] SSL certificate is installed and HTTPS is enforced
- [ ] Session cookies are secure (`SESSION_SECURE_COOKIE=true`)
- [ ] Admin route prefix is unique and secure
- [ ] Payment gateway is configured with production credentials
- [ ] Mail configuration is working (test with registration)
- [ ] Cron jobs are running (check queue processing)
- [ ] Storage symlink is created
- [ ] File permissions are correct (755/644)
- [ ] All caches are optimized
- [ ] Error pages are displaying correctly (test 404, 500)

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
tail -f ~/laravel/storage/logs/laravel.log

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Check file permissions
chmod -R 775 storage bootstrap/cache
```

### Issue: Queue Jobs Not Processing

**Solution:**
```bash
# Verify cron job is running
crontab -l

# Manually process queue to test
php artisan queue:work --stop-when-empty

# Check failed jobs
php artisan queue:failed
```

### Issue: Images Not Displaying

**Solution:**
```bash
# Verify storage symlink
ls -la ~/public_html/storage

# Recreate if needed
php artisan storage:link

# Check file permissions
chmod -R 775 storage/app/public
```

### Issue: Admin Panel 404

**Solution:**
- Verify `ADMIN_ROUTE_PREFIX` in `.env`
- Clear route cache: `php artisan route:clear`
- Check if admin routes are registered in `RouteServiceProvider`

### Issue: Payment Callbacks Failing

**Solution:**
- Verify webhook URL is accessible from internet
- Check payment gateway configuration
- Review logs: `tail -f storage/logs/laravel.log`
- Ensure CSRF is disabled for webhook routes

## Maintenance

### Updating the Application

```bash
# Backup database first!
mysqldump -u username -p database_name > backup.sql

# Pull latest code
cd ~/laravel
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Backup

Add this cron job for daily backups:

```bash
0 2 * * * mysqldump -u username -p'password' database_name | gzip > ~/backups/db-$(date +\%Y\%m\%d).sql.gz
```

### Log Monitoring

```bash
# View recent errors
tail -100 ~/laravel/storage/logs/laravel.log

# Monitor in real-time
tail -f ~/laravel/storage/logs/laravel.log
```

## Performance Optimization

### Enable OPcache

Add to `php.ini` (if you have access):

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### Database Optimization

```bash
# Optimize tables monthly
php artisan db:optimize
```

### Clear Old Logs

```bash
# Clear logs older than 14 days
find ~/laravel/storage/logs -name "*.log" -mtime +14 -delete
```

## Security Recommendations

1. **Change Admin Prefix Regularly**: Update `ADMIN_ROUTE_PREFIX` every few months
2. **Monitor Failed Login Attempts**: Check logs for suspicious activity
3. **Keep Dependencies Updated**: Run `composer update` regularly (in staging first)
4. **Use Strong Database Passwords**: At least 16 characters with mixed case, numbers, symbols
5. **Restrict Database Access**: Only allow localhost connections
6. **Enable Firewall**: Use hosting firewall to block suspicious IPs
7. **Regular Backups**: Automate daily database and weekly file backups

## Support

For deployment issues specific to your hosting provider, consult their documentation or support team.
