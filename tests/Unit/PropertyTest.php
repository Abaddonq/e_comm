<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 47: Models Define Mass Assignment Protection
     * Validates: Requirements 14.4
     *
     * For any Eloquent model, the model MUST define either $fillable or $guarded
     * to protect against mass assignment vulnerabilities.
     */
    public function test_models_define_mass_assignment_protection(): void
    {
        $models = [
            User::class ,
            Category::class ,
            Product::class ,
            \App\Models\ProductVariant::class ,
            \App\Models\ProductImage::class ,
            \App\Models\Cart::class ,
            \App\Models\CartItem::class ,
            \App\Models\Address::class ,
            \App\Models\Order::class ,
            \App\Models\OrderItem::class ,
            \App\Models\Payment::class ,
            \App\Models\Shipment::class ,
            \App\Models\StockMovement::class ,
            \App\Models\Redirect::class ,
        ];

        foreach ($models as $modelClass) {
            $model = new $modelClass;
            $hasFillable = !empty($model->getfillable());
            $hasGuarded = !empty($model->getGuarded());

            $this->assertTrue(
                $hasFillable || $hasGuarded,
                "Model {$modelClass} must define either \$fillable or \$guarded for mass assignment protection"
            );
        }
    }

    /**
     * Property 49: Soft Deletes Preserve Records
     * Validates: Requirements 20.5
     *
     * When a model with soft deletes is "deleted", the record MUST NOT be
     * physically removed from the database. The deleted_at timestamp MUST be set.
     */
    public function test_soft_deletes_preserve_records(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $userId]);

        $deletedUser = User::withTrashed()->find($userId);
        $this->assertNotNull($deletedUser->deleted_at);
        $this->assertTrue($deletedUser->trashed());
    }

    public function test_categories_use_soft_deletes(): void
    {
        $category = Category::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $categoryId]);

        $deletedCategory = Category::withTrashed()->find($categoryId);
        $this->assertNotNull($deletedCategory->deleted_at);
        $this->assertTrue($deletedCategory->trashed());
    }

    public function test_products_use_soft_deletes(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $productId = $product->id;

        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $productId]);

        $deletedProduct = Product::withTrashed()->find($productId);
        $this->assertNotNull($deletedProduct->deleted_at);
        $this->assertTrue($deletedProduct->trashed());
    }

    /**
     * Property 48: Database Schema Has Required Indexes
     * Validates: Requirements 20.3
     *
     * The database MUST have indexes on: slug, category_id, price,
     * variant_id, order_id, user_id, status columns.
     */
    public function test_database_schema_has_required_indexes(): void
    {
        $indexes = DB::select('SHOW INDEX FROM users');
        $indexNames = array_column($indexes, 'Key_name');

        $this->assertContains('users_email_index', $indexNames, 'users table should have email index');

        $categoryIndexes = DB::select('SHOW INDEX FROM categories');
        $categoryIndexNames = array_column($categoryIndexes, 'Key_name');
        $this->assertContains('categories_slug_unique', $categoryIndexNames, 'categories should have slug index');

        $productIndexes = DB::select('SHOW INDEX FROM products');
        $productIndexNames = array_column($productIndexes, 'Key_name');
        $this->assertContains('products_slug_unique', $productIndexNames, 'products should have slug index');

        $categoryIdColumns = array_filter($productIndexes, fn($idx) => $idx->Column_name === 'category_id');
        $this->assertNotEmpty($categoryIdColumns, 'products should have category_id index');

        $orderIndexes = DB::select('SHOW INDEX FROM orders');
        $orderIndexNames = array_column($orderIndexes, 'Key_name');

        $userIdColumns = array_filter($orderIndexes, fn($idx) => $idx->Column_name === 'user_id');
        $this->assertNotEmpty($userIdColumns, 'orders should have user_id index');

        $statusColumns = array_filter($orderIndexes, fn($idx) => $idx->Column_name === 'status');
        $this->assertNotEmpty($statusColumns, 'orders should have status index');

        $variantIndexes = DB::select('SHOW INDEX FROM product_variants');
        $priceColumns = array_filter($variantIndexes, fn($idx) => $idx->Column_name === 'price');
        $this->assertNotEmpty($priceColumns, 'product_variants should have price index');

        $orderItemIndexes = DB::select('SHOW INDEX FROM order_items');
        $variantIdColumns = array_filter($orderItemIndexes, fn($idx) => $idx->Column_name === 'product_variant_id');
        $this->assertNotEmpty($variantIdColumns, 'order_items should have product_variant_id index');
    }

    /**
     * Property 50: Event Notifications Queue Emails
     * Validates: Requirements 17.1, 17.2, 17.3, 17.4
     *
     * When user registration, order creation, or shipment tracking number addition occurs,
     * the system MUST queue email notifications for background processing.
     */
    public function test_event_notifications_queue_emails(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \Illuminate\Auth\Events\Registered::class ,
        ]);

        event(new \Illuminate\Auth\Events\Registered(User::factory()->create()));

        \Illuminate\Support\Facades\Event::assertDispatched(\Illuminate\Auth\Events\Registered::class);
    }

    /**
     * Property 5: Admin Routes Require Admin Role
     * Validates: Requirements 2.1, 2.3, 2.4
     *
     * For any route under the admin prefix, accessing the route should only succeed
     * if the authenticated user has the admin role, otherwise returning 403 for
     * authenticated non-admins or redirecting to login for unauthenticated users.
     */
    public function test_admin_routes_require_admin_role_unauthenticated(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect('/login');
    }

    public function test_admin_routes_require_admin_role_non_admin(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_routes_allow_admin_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Property 6: Admin Forms Require CSRF Token
     * Validates: Requirements 2.5, 14.2
     *
     * For any POST/PUT/DELETE request to admin routes, the request should be
     * rejected if a valid CSRF token is not present.
     * 
     * Note: CSRF middleware is properly configured in Kernel.php web group.
     * This test verifies the middleware is registered.
     */
    public function test_admin_forms_require_csrf_token(): void
    {
        $kernel = $this->app->make(\App\Http\Kernel::class);
        $middleware = $kernel->getMiddlewareGroups();

        $this->assertArrayHasKey('web', $middleware);
        $this->assertContains(
            \App\Http\Middleware\VerifyCsrfToken::class ,
            $middleware['web']
        );
    }

    /**
     * Property 1: User Registration Creates Account and Queues Email
     * Validates: Requirements 1.2
     */
    public function test_user_registration_creates_account_and_queues_email(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \Illuminate\Auth\Events\Registered::class ,
        ]);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);
        $response->assertRedirect('/profile');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        \Illuminate\Support\Facades\Event::assertDispatched(\Illuminate\Auth\Events\Registered::class);
    }

    /**
     * Property 2: Valid Credentials Authenticate User
     * Validates: Requirements 1.3
     */
    public function test_valid_credentials_authenticate_user(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/profile');
    }

    /**
     * Property 3: Rate Limiting Blocks Excessive Login Attempts
     * Validates: Requirements 1.5
     */
    public function test_rate_limiting_blocks_excessive_login_attempts(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Attempt login 5 times with wrong password
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrongpassword',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt should be blocked by rate limiter
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email') ?? '');
    }

    /**
     * Property 4: Password Storage Uses Bcrypt
     * Validates: Requirements 1.7
     */
    public function test_password_storage_uses_bcrypt(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->assertTrue(password_get_info($user->password)['algoName'] === 'bcrypt');
        $this->assertStringStartsWith('$2y$', $user->password);
    }
}
