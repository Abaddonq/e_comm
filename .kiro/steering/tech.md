# Technology Stack

## Framework & Language

- Laravel 10.x
- PHP 8.2+
- MySQL database

## Architecture Pattern

- Monolithic Laravel application with service layer
- Controllers remain thin, business logic in service classes
- Repository pattern not used (direct Eloquent usage)

## Key Dependencies

- Laravel Breeze (authentication scaffolding)
- Intervention Image or similar (image processing)
- Payment gateway SDKs (iyzico and/or Stripe)
- Pest PHP (testing framework)

## Infrastructure Constraints

**Shared Hosting Compatibility:**
- File-based cache driver (no Redis)
- Database queue driver (no Beanstalkd)
- Queue processing via cron jobs (no long-running workers)
- No Docker containers
- No Node.js services
- Standard PHP + MySQL only

## Common Commands

### Development
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Development server
php artisan serve

# Asset compilation
npm run dev
npm run watch
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### Production Optimization
```bash
# Optimize autoloader
composer install --no-dev --optimize-autoloader

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run production

# Clear caches (if needed)
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue Management
```bash
# Process queue jobs (for cron)
php artisan queue:work --stop-when-empty

# Manually process queue
php artisan queue:work

# Prune failed jobs
php artisan queue:prune-failed
```

### Maintenance
```bash
# Generate sitemap
php artisan sitemap:generate

# Clear old logs
php artisan log:clear

# Database backup (custom command)
php artisan backup:database
```

## Caching Strategy

- File-based cache for shared hosting compatibility
- Cache sitemap (1 hour TTL)
- Cache category tree for navigation
- Cache product counts
- Invalidate cache on model updates

## Queue Strategy

- Database queue driver
- Cron job runs `queue:work --stop-when-empty` every minute
- Jobs: email notifications, image processing
- Retry failed jobs 3 times with exponential backoff
