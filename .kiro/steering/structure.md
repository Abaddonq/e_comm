# Project Structure

## Directory Organization

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

database/
├── migrations/               # Database schema migrations
└── seeders/                  # Database seeders

resources/
├── views/
│   ├── layouts/             # Base layouts (app, admin)
│   ├── web/                 # Public views
│   └── admin/               # Admin panel views
└── js/                      # Frontend JavaScript

public/
├── css/                     # Compiled CSS
├── js/                      # Compiled JavaScript
└── storage/                 # Symlink to storage/app/public

routes/
├── web.php                  # Public routes
├── admin.php                # Admin routes (obfuscated prefix)
└── webhooks.php             # Payment callback routes

config/
├── payment.php              # Payment gateway configuration
└── admin.php                # Admin panel configuration
```

## Key Architectural Patterns

### Service Layer
Business logic lives in service classes, not controllers:
- `CartService` - Shopping cart operations
- `OrderService` - Order creation and lifecycle
- `StockService` - Inventory management via movements
- `PaymentService` - Payment gateway abstraction
- `ShippingService` - Courier integration
- `SeoService` - SEO features (slugs, sitemap, schema)
- `ImageService` - Image upload and optimization

### Controller Organization
- **Web controllers**: Thin, delegate to services, return views/JSON
- **Admin controllers**: Protected by AdminMiddleware, manage resources
- **Webhook controllers**: Handle payment callbacks with signature verification

### Model Relationships
- User → hasMany(Order, Address), hasOne(Cart)
- Product → belongsTo(Category), hasMany(ProductVariant, ProductImage)
- ProductVariant → hasMany(StockMovement)
- Order → hasMany(OrderItem), hasOne(Payment, Shipment)
- Cart → hasMany(CartItem)

### Database Schema Conventions
- Soft deletes on: products, categories, users
- Timestamps on all tables
- Foreign keys with appropriate cascade rules
- Indexes on: slugs, foreign keys, status columns, timestamps
- DECIMAL(10,2) for prices
- JSON for flexible attributes (variant attributes, payment responses)

## Critical Design Decisions

### Stock Management
Stock is NEVER stored as a single column. Current stock = sum of all stock_movements for a variant.

Movement types:
- `purchase` - Stock added (positive)
- `sale` - Stock sold (negative)
- `cancellation` - Restored from cancelled order (positive)
- `refund` - Restored from refund (positive)
- `manual_adjustment` - Admin adjustment (positive/negative)

### Order Creation Flow
Wrapped in database transaction:
1. Create order record
2. Create order_items (snapshot product data)
3. Deduct stock via StockService
4. Create shipment via ShippingService
5. Clear cart
6. Queue confirmation email

### Payment Security
All payment callbacks MUST verify signature before processing. Invalid signatures are logged and rejected with 400 status.

### Admin Panel Security
- Routes use obfuscated prefix (configured via env)
- AdminMiddleware checks authentication and role
- CSRF protection on all forms
- Separate layout from public site

### SEO Implementation
- Products and categories use SEO-friendly slugs
- Slug changes create 301 redirects
- Meta tags (title, description) on all pages
- Schema.org Product markup on product pages
- Sitemap.xml generated dynamically, cached for 1 hour
- Canonical URLs to prevent duplicate content

### Image Handling
Uploaded images generate 3 sizes:
- Thumbnail: 150x150
- Medium: 500x500
- Large: 1200x1200

Images converted to WebP with JPEG/PNG fallback. Lazy loading on all product images.

## Naming Conventions

### Models
- Singular, PascalCase: `Product`, `OrderItem`, `StockMovement`
- Use Eloquent conventions for relationships

### Controllers
- Singular resource name + Controller: `ProductController`, `OrderController`
- RESTful method names: index, create, store, show, edit, update, destroy

### Services
- Singular name + Service: `CartService`, `OrderService`
- Public methods use descriptive verbs: `createOrderFromCart`, `deductStockForOrder`

### Migrations
- Format: `YYYY_MM_DD_HHMMSS_create_table_name_table.php`
- Use descriptive names: `create_stock_movements_table`, `add_tracking_to_shipments`

### Routes
- Public: `/products/{slug}`, `/categories/{slug}`, `/cart`, `/checkout`
- Admin: `/{obfuscated-prefix}/products`, `/{obfuscated-prefix}/orders`
- Webhooks: `/webhooks/payment/callback`

## Configuration Files

### Environment Variables
Group by category in .env:
- App: APP_NAME, APP_ENV, APP_DEBUG, APP_KEY, APP_URL
- Database: DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- Cache/Queue: CACHE_DRIVER=file, QUEUE_CONNECTION=database
- Mail: MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
- Payment: PAYMENT_GATEWAY, IYZICO_API_KEY, STRIPE_API_KEY
- Admin: ADMIN_ROUTE_PREFIX

### Custom Config Files
- `config/payment.php` - Payment gateway settings
- `config/admin.php` - Admin panel configuration (route prefix)

## Testing Organization

```
tests/
├── Unit/                    # Service layer unit tests
│   ├── CartServiceTest.php
│   ├── StockServiceTest.php
│   └── OrderServiceTest.php
├── Feature/                 # Integration tests
│   ├── CheckoutFlowTest.php
│   ├── AdminProductTest.php
│   └── PaymentCallbackTest.php
└── Property/                # Property-based tests
    ├── CartPropertiesTest.php
    ├── StockPropertiesTest.php
    └── OrderPropertiesTest.php
```

Property tests run 100 iterations to validate universal correctness properties.
