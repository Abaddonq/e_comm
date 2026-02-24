## SEO Implementation

### URL Structure

**SEO-Friendly Routes:**
```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');
```

**Slug Generation:**
```php
class SeoService
{
    public function generateSlug(string $title, string $modelClass): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        // Ensure uniqueness
        while ($modelClass::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
```

### Meta Tags

**Product Page Meta Tags:**
```blade
{{-- resources/views/products/show.blade.php --}}
@section('meta')
    <title>{{ $product->meta_title ?: $product->title }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $product->meta_description ?: Str::limit($product->description, 160) }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="{{ $product->title }}">
    <meta property="og:description" content="{{ Str::limit($product->description, 200) }}">
    <meta property="og:image" content="{{ asset($product->mainImage?->path) }}">
    <meta property="og:url" content="{{ route('product.show', $product) }}">
    <meta property="og:type" content="product">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $product->title }}">
    <meta name="twitter:description" content="{{ Str::limit($product->description, 200) }}">
    <meta name="twitter:image" content="{{ asset($product->mainImage?->path) }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ route('product.show', $product) }}">
@endsection
```

**Category Page Meta Tags:**
```blade
{{-- resources/views/categories/show.blade.php --}}
@section('meta')
    <title>{{ $category->meta_title ?: $category->name }} | {{ config('app.name') }}</title>
    <meta name="description" content="{{ $category->meta_description ?: $category->description }}">
    <link rel="canonical" href="{{ route('category.show', $category) }}">
    
    {{-- Pagination meta tags --}}
    @if ($products->currentPage() > 1)
        <link rel="prev" href="{{ $products->previousPageUrl() }}">
    @endif
    @if ($products->hasMorePages())
        <link rel="next" href="{{ $products->nextPageUrl() }}">
    @endif
@endsection
```

### Structured Data

**Product Schema.org Markup:**
```php
class SeoService
{
    public function generateProductSchema(Product $product): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => $product->description,
            'image' => $product->images->map(fn($img) => asset($img->path))->toArray(),
            'sku' => $product->variants->first()?->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => config('app.name'),
            ],
            'offers' => [
                '@type' => 'AggregateOffer',
                'priceCurrency' => 'TRY',
                'lowPrice' => $product->minPrice,
                'highPrice' => $product->variants->max('price'),
                'availability' => $product->variants->sum(fn($v) => $v->getCurrentStock()) > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
        
        return $schema;
    }
}
```

**Blade Template:**
```blade
{{-- resources/views/products/show.blade.php --}}
<script type="application/ld+json">
    @json($productSchema)
</script>
```

**Breadcrumb Schema:**
```php
public function generateBreadcrumbSchema(Product $product): array
{
    $items = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => route('home'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $product->category->name,
            'item' => route('category.show', $product->category),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $product->title,
            'item' => route('product.show', $product),
        ],
    ];
    
    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}
```

### Sitemap Generation

**Sitemap Controller:**
```php
class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Cache::remember('sitemap.xml', 3600, function () {
            return $this->generateSitemap();
        });
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml');
    }
    
    private function generateSitemap(): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
        
        // Homepage
        $this->addUrl($xml, route('home'), now(), 'daily', '1.0');
        
        // Categories
        Category::where('is_active', true)->chunk(100, function ($categories) use ($xml) {
            foreach ($categories as $category) {
                $this->addUrl(
                    $xml,
                    route('category.show', $category),
                    $category->updated_at,
                    'weekly',
                    '0.8'
                );
            }
        });
        
        // Products
        Product::where('is_active', true)->chunk(100, function ($products) use ($xml) {
            foreach ($products as $product) {
                $this->addUrl(
                    $xml,
                    route('product.show', $product),
                    $product->updated_at,
                    'weekly',
                    '0.9'
                );
            }
        });
        
        return $xml->asXML();
    }
    
    private function addUrl($xml, string $loc, $lastmod, string $changefreq, string $priority): void
    {
        $url = $xml->addChild('url');
        $url->addChild('loc', $loc);
        $url->addChild('lastmod', $lastmod->toAtomString());
        $url->addChild('changefreq', $changefreq);
        $url->addChild('priority', $priority);
    }
}
```

**Sitemap Route:**
```php
// routes/web.php
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
```

### Robots.txt

**Dynamic Robots.txt:**
```php
class RobotsController extends Controller
{
    public function index()
    {
        $adminPrefix = config('admin.route_prefix');
        
        $content = "User-agent: *\n";
        $content .= "Disallow: /{$adminPrefix}/\n";
        $content .= "Disallow: /cart\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /account\n";
        $content .= "\n";
        $content .= "Sitemap: " . route('sitemap') . "\n";
        
        return response($content)
            ->header('Content-Type', 'text/plain');
    }
}
```

### Redirects

**Redirect Middleware:**
```php
class HandleRedirects
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        
        // Check for redirect
        $redirect = DB::table('redirects')
            ->where('old_path', $path)
            ->first();
        
        if ($redirect) {
            return redirect($redirect->new_path, $redirect->status_code);
        }
        
        return $next($request);
    }
}
```

**Creating Redirects on Slug Change:**
```php
class ProductObserver
{
    public function updating(Product $product): void
    {
        if ($product->isDirty('slug')) {
            $oldSlug = $product->getOriginal('slug');
            $newSlug = $product->slug;
            
            DB::table('redirects')->insert([
                'old_path' => "products/{$oldSlug}",
                'new_path' => "products/{$newSlug}",
                'status_code' => 301,
                'created_at' => now(),
            ]);
        }
    }
}
```

## Deployment Architecture

### Shared Hosting Structure

**Directory Layout:**
```
/home/username/
├── public_html/              # Web root (symlink to laravel/public)
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── storage -> ../laravel/storage/app/public
├── laravel/                  # Laravel application
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/              # Original public folder
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   └── composer.json
└── backups/                 # Database backups
```

**Alternative: Copy public contents to public_html:**
```
/home/username/
├── public_html/              # Web root
│   ├── index.php            # Modified to point to ../laravel
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── storage -> ../laravel/storage/app/public
└── laravel/                  # Laravel application (one level up)
    ├── app/
    ├── bootstrap/
    ├── config/
    └── ...
```

**Modified index.php for public_html:**
```php
<?php
// public_html/index.php

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__.'/../laravel/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
```

### Deployment Process

**Step 1: Upload Files**
```bash
# Via FTP/SFTP, upload:
# - Laravel application to /home/username/laravel/
# - Exclude: node_modules, .git, tests, storage/logs/*

# Or use rsync (if SSH access available)
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude 'storage/logs/*' \
    ./ username@host:/home/username/laravel/
```

**Step 2: Configure Environment**
```bash
# SSH into server (if available) or use cPanel File Manager
cd /home/username/laravel

# Copy environment file
cp .env.example .env

# Edit .env with production values
nano .env
```

**.env Production Configuration:**
```env
APP_NAME="Your Store"
APP_ENV=production
APP_KEY=base64:GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

PAYMENT_GATEWAY=iyzico
IYZICO_API_KEY=your_api_key
IYZICO_SECRET_KEY=your_secret_key
IYZICO_BASE_URL=https://api.iyzipay.com

ADMIN_ROUTE_PREFIX=secure-admin-xyz123
ADMIN_EMAIL=admin@yourdomain.com
```

**Step 3: Install Dependencies**
```bash
# SSH required for this step
cd /home/username/laravel

# Install Composer dependencies (production only)
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link
```

**Step 4: Setup Public Directory**
```bash
# Option A: Symlink (if supported)
ln -s /home/username/laravel/public /home/username/public_html

# Option B: Copy contents and modify index.php
cp -r /home/username/laravel/public/* /home/username/public_html/
# Then edit public_html/index.php to point to ../laravel
```

**Step 5: Database Setup**
```bash
# Run migrations
php artisan migrate --force

# Seed initial data (admin user, categories)
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=CategorySeeder
```

**Step 6: Optimize for Production**
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

**Step 7: Set File Permissions**
```bash
# Storage and cache directories must be writable
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache

# Or via cPanel File Manager: Set permissions to 755 for directories, 644 for files
```

**Step 8: Configure Cron Job**
```bash
# cPanel > Cron Jobs
# Add this command to run every minute:
* * * * * cd /home/username/laravel && php artisan schedule:run >> /dev/null 2>&1
```

### Environment-Specific Configuration

**Development (.env.local):**
```env
APP_ENV=local
APP_DEBUG=true
LOG_LEVEL=debug

DB_DATABASE=ecommerce_dev

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log

PAYMENT_GATEWAY=iyzico
IYZICO_BASE_URL=https://sandbox-api.iyzipay.com
```

**Staging (.env.staging):**
```env
APP_ENV=staging
APP_DEBUG=false
LOG_LEVEL=info

DB_DATABASE=ecommerce_staging

CACHE_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp

PAYMENT_GATEWAY=iyzico
IYZICO_BASE_URL=https://sandbox-api.iyzipay.com
```

### Backup Strategy

**Database Backup Script:**
```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/username/backups"
DB_NAME="your_database"
DB_USER="your_username"
DB_PASS="your_password"

# Create backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
```

**Cron Job for Backups:**
```bash
# Daily at 2 AM
0 2 * * * /home/username/backup.sh
```

**Laravel Backup Package (Alternative):**
```bash
composer require spatie/laravel-backup

# Configure in config/backup.php
# Run backup
php artisan backup:run

# Schedule in app/Console/Kernel.php
$schedule->command('backup:run')->daily()->at('02:00');
```

### Monitoring and Maintenance

**Health Check Endpoint:**
```php
// routes/web.php
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'ok';
    } catch (\Exception $e) {
        $dbStatus = 'error';
    }
    
    return response()->json([
        'status' => $dbStatus === 'ok' ? 'healthy' : 'unhealthy',
        'database' => $dbStatus,
        'cache' => Cache::has('health_check') ? 'ok' : 'error',
        'storage' => is_writable(storage_path()) ? 'ok' : 'error',
    ]);
});
```

**Log Monitoring:**
```bash
# View recent errors
tail -f storage/logs/laravel.log

# View payment logs
tail -f storage/logs/payment.log

# View security logs
tail -f storage/logs/security.log
```

**Performance Monitoring:**
```php
// app/Http/Middleware/PerformanceMonitoring.php
class PerformanceMonitoring
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        // Log slow requests (> 2 seconds)
        if ($duration > 2) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration' => $duration,
                'memory' => memory_get_peak_usage(true) / 1024 / 1024 . 'MB',
            ]);
        }
        
        return $response;
    }
}
```

### Troubleshooting Common Issues

**Issue: 500 Internal Server Error**
```bash
# Check Laravel logs
tail -n 50 storage/logs/laravel.log

# Check Apache/PHP error logs
tail -n 50 /var/log/apache2/error.log

# Common causes:
# - Missing .env file
# - Wrong file permissions
# - Missing APP_KEY
# - Database connection issues
```

**Issue: Storage symlink not working**
```bash
# Remove existing symlink
rm public/storage

# Recreate
php artisan storage:link

# Or manually create
ln -s /home/username/laravel/storage/app/public /home/username/public_html/storage
```

**Issue: Queue jobs not processing**
```bash
# Check cron is running
crontab -l

# Manually process queue
php artisan queue:work --stop-when-empty

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

**Issue: Cache not clearing**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Manually delete cache files
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
```

