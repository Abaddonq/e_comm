# DecoreMotto E-Commerce Platform - Setup Guide

## Overview

This is a production-ready Laravel 10.x e-commerce platform designed for selling decorative physical products. The system is optimized for shared hosting environments (Hostinger Business Plan) and handles the complete order lifecycle from product browsing through payment processing to shipment tracking.

## System Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer 2.x
- Node.js and NPM (for asset compilation)

## Infrastructure Characteristics

### Shared Hosting Compatible
- **Cache Driver**: File-based (no Redis required)
- **Queue Driver**: Database-based (no Beanstalkd required)
- **Queue Processing**: Cron-based (no long-running workers)
- **No Docker**: Standard PHP + MySQL only
- **No Node.js Services**: Assets compiled during deployment

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/              # Public-facing controllers
│   │   ├── Admin/            # Admin panel controllers
│   │   └── Webhooks/         # Payment callback handlers
│   ├── Middleware/           # Custom middleware
│   └── Requests/             # Form request validation classes
├── Services/                 # Business logic layer
├── Models/                   # Eloquent models
├── Contracts/                # Interfaces (PaymentGateway, Courier)
├── Integrations/             # External service implementations
│   ├── Payment/
│   └── Shipping/
└── Jobs/                     # Queue jobs

routes/
├── web.php                  # Public routes
├── admin.php                # Admin routes (obfuscated prefix)
└── webhooks.php             # Payment callback routes

config/
├── payment.php              # Payment gateway configuration
└── admin.php                # Admin panel configuration
```

## Installation Steps

### 1. Clone and Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Environment Variables

Edit `.env` file and configure the following:

#### Application Settings
```env
APP_NAME="DecoreMotto E-Commerce"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
```

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=decoremotto
DB_USERNAME=root
DB_PASSWORD=
```

#### Cache & Queue (Shared Hosting Compatible)
```env
CACHE_DRIVER=file
QUEUE_CONNECTION=database
```

#### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@decoremotto.com"
```

#### Payment Gateway Configuration
```env
# Choose: 'iyzico' or 'stripe'
PAYMENT_GATEWAY=iyzico

# iyzico Configuration
IYZICO_API_KEY=
IYZICO_SECRET_KEY=
IYZICO_BASE_URL=https://sandbox-api.iyzipay.com

# Stripe Configuration
STRIPE_API_KEY=
STRIPE_WEBHOOK_SECRET=
```

#### Admin Panel Security
```env
# Obfuscated admin route prefix
ADMIN_ROUTE_PREFIX=secure-admin-xyz123
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Run seeders (when available)
php artisan db:seed
```

### 5. Storage Setup

```bash
# Create storage symlink
php artisan storage:link
```

### 6. Asset Compilation

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

## Key Features

### Core Capabilities
- Product catalog with categories, variants, and images
- Shopping cart for authenticated and guest users
- Secure checkout with payment gateway integration (iyzico/Stripe)
- Order management with automated stock tracking
- Shipment tracking and notifications
- Admin panel for product, order, and inventory management
- SEO optimization with slugs, meta tags, and sitemap
- Email notifications for key events

### Architecture Highlights
- **Service Layer Pattern**: Business logic isolated in service classes
- **Stock Management**: Event-driven stock tracking via movements table
- **Payment Security**: Signature verification for all payment callbacks
- **Admin Security**: Obfuscated routes and role-based access control
- **SEO Optimized**: Slugs, meta tags, structured data, sitemap generation
- **Image Optimization**: Multiple sizes, WebP conversion, lazy loading

## Admin Panel Access

Admin panel URL format:
```
https://yoursite.com/{ADMIN_ROUTE_PREFIX}/dashboard
```

Example with default prefix:
```
http://localhost:8000/secure-admin-xyz123/dashboard
```

## Queue Processing

For shared hosting, add this cron job (runs every minute):
```bash
* * * * * cd /path/to/project && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

## Production Deployment

### Optimization Commands
```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

### Security Checklist
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Configure secure session cookies
- [ ] Set strong `ADMIN_ROUTE_PREFIX`
- [ ] Configure SSL/HTTPS
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Configure payment gateway production credentials

## Development Guidelines

### Service Layer
All business logic should be in service classes:
- `CartService` - Shopping cart operations
- `OrderService` - Order creation and lifecycle
- `StockService` - Inventory management via movements
- `PaymentService` - Payment gateway abstraction
- `ShippingService` - Courier integration
- `SeoService` - SEO features
- `ImageService` - Image upload and optimization

### Controller Pattern
Controllers should be thin and delegate to services:
```php
public function store(Request $request)
{
    $result = $this->cartService->addItem($cart, $variant, $quantity);
    return response()->json($result);
}
```

### Stock Management
Stock is NEVER stored as a single column. Current stock = sum of all stock_movements for a variant.

Movement types:
- `purchase` - Stock added (positive)
- `sale` - Stock sold (negative)
- `cancellation` - Restored from cancelled order (positive)
- `refund` - Restored from refund (positive)
- `manual_adjustment` - Admin adjustment (positive/negative)

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## Support

For issues and questions, refer to the project documentation or contact the development team.

## License

Proprietary - All rights reserved
