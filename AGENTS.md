# DecorMotto - Laravel E-Commerce Platform

## Deployment Guide

### Requirements
- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for asset building)
- Apache/Nginx

### Installation

1. Clone repository and install dependencies:
```bash
composer install --no-dev
npm install && npm run build
```

2. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

3. Update `.env` with production values:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=decoremotto
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

PAYMENT_GATEWAY=iyzico
IYZICO_API_KEY=your_api_key
IYZICO_SECRET_KEY=your_secret_key
IYZICO_BASE_URL=https://api.iyzipay.com

STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
```

4. Run migrations and seeders:
```bash
php artisan migrate --force
php artisan db:seed --force
```

5. Optimize for production:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Shared Hosting Configuration

For shared hosting (cPanel), modify `public/index.php`:

```php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### Cron Jobs

Add to cPanel Cron Jobs:
```
* * * * * /usr/local/bin/php /home/username/decoremotto/artisan schedule:run >> /dev/null 2>&1
```

### Queue Processing

For shared hosting, use database queue:
```
QUEUE_CONNECTION=database
```

Monitor queue:
```bash
php artisan queue:work
```

### File Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/uploads
```

### Troubleshooting

**500 Error:**
- Check `storage/logs/laravel.log`
- Run `php artisan config:clear`
- Verify `.env` configuration

**Images not uploading:**
- Verify `storage` folder is writable
- Check disk space quota

**Payment issues:**
- Verify gateway credentials in `.env`
- Check webhook URLs are configured

### Admin Access

After seeding:
- URL: `https://yourdomain.com/secure-admin-xyz123`
- Email: admin@decoremotto.com
- Password: admin123

### Commands

```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan optimize
php artisan config:cache
php artisan route:cache

# Database
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Queue
php artisan queue:restart
php artisan queue:work
```
