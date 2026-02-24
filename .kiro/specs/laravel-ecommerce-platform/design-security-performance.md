## Security Measures

### Authentication and Authorization

**Password Security:**
- All passwords hashed using bcrypt (Laravel default)
- Minimum password length: 8 characters
- Password confirmation required on registration
- Password reset via secure token (expires in 60 minutes)

**Session Security:**
```php
// config/session.php
'lifetime' => 120, // 2 hours
'expire_on_close' => false,
'encrypt' => true,
'http_only' => true,
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only in production
'same_site' => 'lax',
```

**Rate Limiting:**
```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

RateLimiter::for('checkout', function (Request $request) {
    return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

### CSRF Protection

**Middleware Configuration:**
```php
// app/Http/Middleware/VerifyCsrfToken.php
class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'webhooks/payment/callback', // Payment gateway callbacks use signature verification
    ];
}
```

**Blade Templates:**
```blade
<form method="POST" action="/checkout">
    @csrf
    <!-- form fields -->
</form>
```

### XSS Prevention

**Output Escaping:**
```blade
{{-- Safe: Automatically escaped --}}
<p>{{ $userInput }}</p>

{{-- Unsafe: Only use for trusted admin content --}}
<div>{!! $adminContent !!}</div>

{{-- Safe: Escape in JavaScript context --}}
<script>
    const data = @json($data);
</script>
```

**Content Security Policy:**
```php
// app/Http/Middleware/SecurityHeaders.php
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        return $response;
    }
}
```

### SQL Injection Prevention

**Eloquent ORM:**
```php
// Safe: Parameterized queries
Product::where('slug', $slug)->first();
Product::whereIn('id', $ids)->get();

// Safe: Query builder with bindings
DB::table('products')
    ->where('price', '>', $minPrice)
    ->where('category_id', $categoryId)
    ->get();

// Unsafe: Raw queries without bindings (NEVER DO THIS)
// DB::select("SELECT * FROM products WHERE slug = '$slug'");

// Safe: Raw queries with bindings
DB::select('SELECT * FROM products WHERE slug = ?', [$slug]);
```

**Mass Assignment Protection:**
```php
class Product extends Model
{
    // Whitelist approach (recommended)
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'is_active',
        'featured',
    ];
    
    // OR Blacklist approach
    // protected $guarded = ['id', 'created_at', 'updated_at'];
}
```

### Payment Security

**Signature Verification:**
```php
class IyzicoGateway implements PaymentGatewayInterface
{
    public function verifyCallback(array $callbackData): bool
    {
        $receivedSignature = $callbackData['signature'] ?? '';
        
        // Build signature string from callback data
        $signatureString = $this->buildSignatureString($callbackData);
        
        // Calculate expected signature
        $expectedSignature = hash_hmac('sha256', $signatureString, $this->secretKey);
        
        // Constant-time comparison to prevent timing attacks
        return hash_equals($expectedSignature, $receivedSignature);
    }
    
    private function buildSignatureString(array $data): string
    {
        // Sort keys alphabetically
        ksort($data);
        
        // Build string according to gateway specification
        $parts = [];
        foreach ($data as $key => $value) {
            if ($key !== 'signature') {
                $parts[] = $key . '=' . $value;
            }
        }
        
        return implode('&', $parts);
    }
}
```

**PCI Compliance:**
- Never store credit card numbers, CVV, or full card data
- All payment data handled by PCI-compliant gateway (iyzico/Stripe)
- Only store transaction IDs and payment status
- Use HTTPS for all payment-related pages

### File Upload Security

**Image Upload Validation:**
```php
class ImageService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];
    
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    
    public function uploadProductImage(UploadedFile $file, Product $product): ProductImage
    {
        // Validate MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new ValidationException('Invalid file type. Only JPEG, PNG, and WebP are allowed.');
        }
        
        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new ValidationException('File size exceeds 5MB limit.');
        }
        
        // Generate secure filename
        $filename = Str::random(40) . '.' . $file->extension();
        
        // Store in product-specific directory
        $path = $file->storeAs(
            "products/{$product->id}",
            $filename,
            'public'
        );
        
        // Create database record
        return ProductImage::create([
            'product_id' => $product->id,
            'path' => $path,
            'alt_text' => $product->title,
        ]);
    }
}
```

### Environment Configuration Security

**.env File Protection:**
```apache
# public/.htaccess
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

**.gitignore:**
```
.env
.env.backup
.env.production
```

**Environment Validation:**
```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    if (app()->environment('production')) {
        // Validate critical environment variables
        $required = [
            'APP_KEY',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'PAYMENT_GATEWAY',
            'MAIL_MAILER',
        ];
        
        foreach ($required as $var) {
            if (empty(env($var))) {
                throw new \RuntimeException("Required environment variable {$var} is not set");
            }
        }
        
        // Ensure debug is off
        if (config('app.debug')) {
            throw new \RuntimeException('APP_DEBUG must be false in production');
        }
    }
}
```

### Admin Panel Security

**Route Obfuscation:**
```php
// config/admin.php
return [
    'route_prefix' => env('ADMIN_ROUTE_PREFIX', 'management-' . substr(md5(env('APP_KEY')), 0, 12)),
];

// .env
ADMIN_ROUTE_PREFIX=secure-panel-a1b2c3d4e5f6
```

**Admin Middleware:**
```php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please log in to continue.');
        }
        
        if (!auth()->user()->isAdmin()) {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            
            abort(403, 'Unauthorized access');
        }
        
        return $next($request);
    }
}
```

**IP Whitelisting (Optional):**
```php
// config/admin.php
'allowed_ips' => env('ADMIN_ALLOWED_IPS') ? explode(',', env('ADMIN_ALLOWED_IPS')) : [],

// Middleware
if (!empty(config('admin.allowed_ips'))) {
    if (!in_array($request->ip(), config('admin.allowed_ips'))) {
        abort(403, 'Access denied from this IP address');
    }
}
```



## Performance Optimization

### Database Optimization

**Query Optimization:**
```php
// Bad: N+1 query problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Separate query for each product
    foreach ($product->images as $image) { // Separate query for each product
        echo $image->path;
    }
}

// Good: Eager loading
$products = Product::with(['category', 'images'])->get();

// Better: Selective eager loading
$products = Product::with([
    'category:id,name,slug',
    'images' => fn($q) => $q->select('id', 'product_id', 'thumbnail_path')->limit(1),
    'variants' => fn($q) => $q->where('is_active', true)->select('id', 'product_id', 'price'),
])->get();

// Best: Pagination for large datasets
$products = Product::with(['category:id,name', 'images' => fn($q) => $q->limit(1)])
    ->paginate(20);
```

**Index Strategy:**
```sql
-- Single column indexes (already in schema)
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_category_id ON products(category_id);
CREATE INDEX idx_orders_user_id ON orders(user_id);

-- Composite indexes for common queries
CREATE INDEX idx_products_category_active ON products(category_id, is_active, created_at);
CREATE INDEX idx_orders_user_status ON orders(user_id, status, created_at);
CREATE INDEX idx_stock_variant_type ON stock_movements(product_variant_id, movement_type, created_at);

-- Covering indexes for specific queries
CREATE INDEX idx_products_list ON products(category_id, is_active, id, title, slug);
```

**Query Caching:**
```php
// Cache expensive queries
$categories = Cache::remember('categories.tree', 3600, function () {
    return Category::with('children')
        ->whereNull('parent_id')
        ->orderBy('sort_order')
        ->get();
});

// Cache product counts
$productCount = Cache::remember("category.{$categoryId}.count", 1800, function () use ($categoryId) {
    return Product::where('category_id', $categoryId)
        ->where('is_active', true)
        ->count();
});

// Invalidate cache on updates
Product::created(function ($product) {
    Cache::forget("category.{$product->category_id}.count");
});
```

### Caching Strategy

**File-Based Cache Configuration:**
```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'file'),

'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
],
```

**Cache Usage Patterns:**
```php
// View caching
public function show(Product $product)
{
    $cacheKey = "product.{$product->id}.view";
    
    $data = Cache::remember($cacheKey, 3600, function () use ($product) {
        return [
            'product' => $product->load(['category', 'variants', 'images']),
            'relatedProducts' => $this->getRelatedProducts($product),
        ];
    });
    
    return view('products.show', $data);
}

// Fragment caching in Blade
@cache('sidebar.categories', 3600)
    <ul>
        @foreach($categories as $category)
            <li><a href="{{ route('category.show', $category) }}">{{ $category->name }}</a></li>
        @endforeach
    </ul>
@endcache
```

**Cache Invalidation:**
```php
// Model observers for cache invalidation
class ProductObserver
{
    public function saved(Product $product): void
    {
        Cache::forget("product.{$product->id}.view");
        Cache::forget("category.{$product->category_id}.products");
        Cache::forget('sitemap.xml');
    }
    
    public function deleted(Product $product): void
    {
        Cache::forget("product.{$product->id}.view");
        Cache::forget("category.{$product->category_id}.products");
        Cache::forget('sitemap.xml');
    }
}
```

### Asset Optimization

**Laravel Mix Configuration:**
```javascript
// webpack.mix.js
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('postcss-import'),
       require('tailwindcss'),
       require('autoprefixer'),
   ])
   .version(); // Cache busting

if (mix.inProduction()) {
    mix.minify('public/js/app.js')
       .minify('public/css/app.css');
}
```

**Image Optimization:**
```php
class ImageService
{
    public function generateThumbnails(ProductImage $image): void
    {
        $sizes = [
            'thumbnail' => [150, 150],
            'medium' => [500, 500],
            'large' => [1200, 1200],
        ];
        
        foreach ($sizes as $name => $dimensions) {
            $img = Image::make(storage_path('app/public/' . $image->path));
            
            // Resize maintaining aspect ratio
            $img->fit($dimensions[0], $dimensions[1], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Optimize quality
            $img->encode('webp', 85);
            
            // Save
            $path = str_replace('.', "_{$name}.", $image->path);
            $img->save(storage_path('app/public/' . $path));
            
            // Update database
            $image->update(["{$name}_path" => $path]);
        }
    }
}
```

**Lazy Loading:**
```blade
{{-- Native lazy loading for images --}}
<img src="{{ asset($image->path) }}" 
     alt="{{ $image->alt_text }}" 
     loading="lazy"
     width="500" 
     height="500">

{{-- Responsive images with WebP --}}
<picture>
    <source srcset="{{ asset($image->medium_path) }}" type="image/webp">
    <img src="{{ asset($image->path) }}" 
         alt="{{ $image->alt_text }}" 
         loading="lazy">
</picture>
```

### Queue Optimization

**Database Queue Configuration:**
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],
],
```

**Job Processing:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Process queued jobs every minute
    $schedule->command('queue:work --stop-when-empty --max-time=50')
             ->everyMinute()
             ->withoutOverlapping();
    
    // Clean up old failed jobs
    $schedule->command('queue:prune-failed --hours=168')
             ->weekly();
}
```

**Job Optimization:**
```php
class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $timeout = 30;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    
    public function __construct(
        public Order $order
    ) {}
    
    public function handle(): void
    {
        Mail::to($this->order->user->email)
            ->send(new OrderConfirmation($this->order));
    }
    
    public function failed(Throwable $exception): void
    {
        Log::error('Order confirmation email failed', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
```

### Response Optimization

**HTTP Caching:**
```php
// Controller method
public function show(Product $product)
{
    $response = response()->view('products.show', compact('product'));
    
    // Cache for 1 hour
    $response->setCache([
        'public' => true,
        'max_age' => 3600,
        's_maxage' => 3600,
    ]);
    
    // Set Last-Modified header
    $response->setLastModified($product->updated_at);
    
    return $response;
}
```

**Compression:**
```apache
# public/.htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

**Browser Caching:**
```apache
# public/.htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Shared Hosting Optimizations

**Composer Optimization:**
```bash
# Production deployment
composer install --no-dev --optimize-autoloader

# Generate optimized class map
php artisan optimize
```

**Configuration Caching:**
```bash
# Cache configuration files
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

**Opcache Configuration:**
```ini
; php.ini (if accessible)
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

