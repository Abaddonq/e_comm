# DecorMotto - Laravel E-Commerce Platform

A production-ready Laravel e-commerce platform designed for selling decorative physical products. Built with Laravel 10.x and optimized for shared hosting environments.

## Features

### Customer Features
- 🛍️ Product catalog with categories, variants, and multiple images
- 🛒 Shopping cart for authenticated and guest users
- 💳 Secure checkout with payment gateway integration (iyzico/Stripe)
- 📦 Order tracking and shipment notifications
- 💌 Wishlist functionality
- 👤 User profiles with address management
- 🔍 Product search and filtering
- 📱 Responsive design for mobile and desktop

### Admin Features
- 📊 Product management with variants and inventory
- 📦 Order management and fulfillment
- 📈 Stock tracking via movement-based system
- 🖼️ Image upload with automatic optimization
- 🏷️ Category management with SEO-friendly slugs
- 📧 Automated email notifications
- 🔐 Secure admin panel with obfuscated routes

### Technical Features
- ⚡ Optimized for shared hosting (no Docker/Redis required)
- 🔒 Security best practices (CSRF, XSS protection, secure headers)
- 📧 Queue-based email processing
- 🎨 SEO optimization (meta tags, sitemap, schema.org markup)
- 🖼️ WebP image conversion with fallbacks
- 📱 Lazy loading for images
- 🔄 301 redirects for changed slugs
- ✅ Comprehensive test suite with property-based testing

## Technology Stack

- **Framework:** Laravel 10.x
- **Language:** PHP 8.2+
- **Database:** MySQL 8.0+ / MariaDB 10.3+
- **Authentication:** Laravel Breeze
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js
- **Testing:** Pest PHP
- **Payment Gateways:** iyzico, Stripe
- **Image Processing:** Intervention Image

## Requirements

- PHP 8.2 or higher
- MySQL 8.0+ or MariaDB 10.3+
- Composer
- Node.js & NPM
- Apache/Nginx web server

### PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD or Imagick

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/decormotto.git
cd decormotto
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your configuration:

```env
APP_NAME=DecorMotto
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=decormotto
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@decormotto.com"
MAIL_FROM_NAME="${APP_NAME}"

# Payment Gateway (iyzico or stripe)
PAYMENT_GATEWAY=iyzico

# iyzico Configuration
IYZICO_API_KEY=your_api_key
IYZICO_SECRET_KEY=your_secret_key
IYZICO_BASE_URL=https://sandbox-api.iyzipay.com

# Stripe Configuration
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx

# Admin Panel
ADMIN_ROUTE_PREFIX=admin-panel-xyz
```

### 4. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

```bash
npm run dev
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` to see your application.

## Production Deployment

### Shared Hosting (Hostinger, cPanel)

#### 1. Upload Files

Upload all files except `node_modules` and `vendor` to your hosting account.

#### 2. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

#### 3. Configure Environment

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

#### 4. Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --force
```

#### 5. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### 6. Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 777 storage/uploads
```

#### 7. Configure Cron Jobs

Add to cPanel Cron Jobs (runs every minute):

```bash
* * * * * /usr/local/bin/php /home/username/decormotto/artisan schedule:run >> /dev/null 2>&1
```

#### 8. Configure Queue Processing

For shared hosting, the cron job will process queued jobs:

```bash
* * * * * /usr/local/bin/php /home/username/decormotto/artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

### VPS/Dedicated Server

For VPS deployment, you can use supervisor for queue workers:

```ini
[program:decormotto-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/decormotto/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/decormotto/storage/logs/worker.log
```

## Configuration

### Payment Gateways

#### iyzico Setup

1. Register at [iyzico](https://www.iyzico.com/)
2. Get API credentials from dashboard
3. Update `.env`:

```env
PAYMENT_GATEWAY=iyzico
IYZICO_API_KEY=your_api_key
IYZICO_SECRET_KEY=your_secret_key
IYZICO_BASE_URL=https://api.iyzipay.com
```

4. Configure webhook URL: `https://yourdomain.com/webhooks/payment/callback`

#### Stripe Setup

1. Register at [Stripe](https://stripe.com/)
2. Get API keys from dashboard
3. Update `.env`:

```env
PAYMENT_GATEWAY=stripe
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
```

4. Configure webhook URL in Stripe dashboard

### Email Configuration

Update `.env` with your SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="DecorMotto"
```

### Admin Panel Access

Default admin credentials (change after first login):

- URL: `https://yourdomain.com/{ADMIN_ROUTE_PREFIX}`
- Email: Set via `ADMIN_SEED_EMAIL` in `.env`
- Password: Set via `ADMIN_SEED_PASSWORD` in `.env`

**Security Note:** Change `ADMIN_ROUTE_PREFIX` to a random string to obscure admin panel URL.

## Usage

### Common Commands

#### Development

```bash
# Start development server
php artisan serve

# Watch for asset changes
npm run dev

# Run tests
php artisan test

# Clear all caches
php artisan optimize:clear
```

#### Production

```bash
# Optimize application
php artisan optimize

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Queue Management

```bash
# Process queue jobs
php artisan queue:work

# Process queue and stop when empty (for cron)
php artisan queue:work --stop-when-empty

# Restart queue workers
php artisan queue:restart

# Prune failed jobs
php artisan queue:prune-failed
```

#### Maintenance

```bash
# Generate sitemap
php artisan sitemap:generate

# Clear old logs
php artisan log:clear

# Enter maintenance mode
php artisan down

# Exit maintenance mode
php artisan up
```

### Testing

Run the complete test suite:

```bash
php artisan test
```

Run specific test suites:

```bash
# Unit tests
php artisan test --testsuite=Unit

# Feature tests
php artisan test --testsuite=Feature

# Property-based tests
php artisan test --testsuite=Property
```

Run with coverage:

```bash
php artisan test --coverage
```

## Architecture

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/              # Public-facing controllers
│   │   ├── Admin/            # Admin panel controllers
│   │   └── Webhooks/         # Payment callback handlers
│   ├── Middleware/           # Custom middleware
│   └── Requests/             # Form request validation
├── Services/                 # Business logic layer
├── Models/                   # Eloquent models
├── Jobs/                     # Queue jobs
└── Mail/                     # Email templates

resources/
├── views/
│   ├── web/                 # Public views
│   └── admin/               # Admin panel views
└── js/                      # Frontend JavaScript

database/
├── migrations/              # Database schema
└── seeders/                 # Database seeders
```

### Service Layer

Business logic is organized in service classes:

- `CartService` - Shopping cart operations
- `OrderService` - Order creation and lifecycle
- `StockService` - Inventory management
- `PaymentService` - Payment gateway abstraction
- `ShippingService` - Courier integration
- `SeoService` - SEO features
- `ImageService` - Image processing

### Stock Management

Stock is tracked via movements (never as a single column):

- `purchase` - Stock added (positive)
- `sale` - Stock sold (negative)
- `cancellation` - Restored from cancelled order (positive)
- `refund` - Restored from refund (positive)
- `manual_adjustment` - Admin adjustment (positive/negative)

Current stock = sum of all movements for a variant.

### Order Flow

1. User adds products to cart
2. Proceeds to checkout
3. Payment processed via gateway
4. Order created (transaction-wrapped)
5. Stock deducted via movements
6. Shipment created
7. Confirmation email queued
8. Cart cleared

## Security

### Implemented Security Measures

- CSRF protection on all forms
- XSS protection via Blade escaping
- SQL injection prevention via Eloquent ORM
- Password hashing with bcrypt
- Secure session management
- Rate limiting on checkout
- Payment webhook signature verification
- Admin panel route obfuscation
- Security headers middleware
- Input validation via Form Requests

### Best Practices

1. Never commit `.env` file
2. Use strong passwords for admin accounts
3. Keep dependencies updated
4. Enable HTTPS in production
5. Configure firewall rules
6. Regular database backups
7. Monitor error logs
8. Use environment-specific configurations

## Troubleshooting

### Common Issues

#### 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Verify permissions
chmod -R 755 storage bootstrap/cache
```

#### Images Not Uploading

```bash
# Check storage link
php artisan storage:link

# Verify permissions
chmod -R 777 storage/uploads

# Check disk space
df -h
```

#### Payment Callback Failing

1. Verify webhook URL is configured in gateway dashboard
2. Check signature verification in logs
3. Ensure HTTPS is enabled
4. Verify API credentials in `.env`

#### Queue Jobs Not Processing

```bash
# Check queue connection
php artisan queue:work

# Verify cron job is running
crontab -l

# Check failed jobs
php artisan queue:failed
```

#### Database Connection Error

1. Verify database credentials in `.env`
2. Check database server is running
3. Ensure database exists
4. Verify user has proper permissions

## Performance Optimization

### Caching Strategy

- Configuration caching: `php artisan config:cache`
- Route caching: `php artisan route:cache`
- View caching: `php artisan view:cache`
- Sitemap caching (1 hour TTL)
- Category tree caching
- Product count caching

### Image Optimization

- WebP conversion with JPEG/PNG fallback
- Three sizes: thumbnail (150x150), medium (500x500), large (1200x1200)
- Lazy loading on all product images
- Optimized compression settings

### Database Optimization

- Indexes on foreign keys, slugs, status columns
- Eager loading to prevent N+1 queries
- Query result caching where appropriate
- Soft deletes for data retention

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation as needed
- Use meaningful commit messages

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, email support@decormotto.com or open an issue on GitHub.

## Acknowledgments

- Built with [Laravel](https://laravel.com/)
- UI components from [Tailwind CSS](https://tailwindcss.com/)
- Icons from [Heroicons](https://heroicons.com/)
- Testing with [Pest PHP](https://pestphp.com/)

---

**Note:** This is a production-ready e-commerce platform. Ensure all security measures are properly configured before deploying to production.
