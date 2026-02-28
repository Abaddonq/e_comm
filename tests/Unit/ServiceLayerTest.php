<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Services\CartService;
use App\Services\StockService;
use App\Services\OrderService;
use App\Services\SeoService;
use App\Services\ShippingService;
use App\Services\PaymentService;
use App\Services\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceLayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_service_creates_cart_for_user(): void
    {
        $user = User::factory()->create();
        $cartService = new CartService();

        $cart = $cartService->getOrCreateCart($user->id);

        $this->assertNotNull($cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_cart_service_creates_cart_for_guest(): void
    {
        $sessionId = 'test-session-123';
        $cartService = new CartService();

        $cart = $cartService->getOrCreateCart(null, $sessionId);

        $this->assertNotNull($cart);
        $this->assertEquals($sessionId, $cart->session_id);
    }

    public function test_cart_service_adds_item_to_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $variant = ProductVariant::factory()->create(['price' => 100]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $cartService = new CartService();
        $item = $cartService->addItem($cart, $variant->id, 2);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    public function test_cart_service_updates_existing_item_quantity(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $variant = ProductVariant::factory()->create(['price' => 100]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        $cartService = new CartService();
        $cartService->addItem($cart, $variant->id, 3);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
        ]);
    }

    public function test_cart_service_calculates_total(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $variant1 = ProductVariant::factory()->create(['price' => 100]);
        $variant2 = ProductVariant::factory()->create(['price' => 50]);

        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant1->id, 'quantity' => 2, 'price' => 100]);
        CartItem::create(['cart_id' => $cart->id, 'product_variant_id' => $variant2->id, 'quantity' => 1, 'price' => 50]);

        $cartService = new CartService();
        $result = $cartService->calculateTotal($cart);

        $this->assertEquals(250, $result['subtotal']);
        $this->assertEquals(3, $result['item_count']);
    }

    public function test_stock_service_gets_current_stock(): void
    {
        $variant = ProductVariant::factory()->create();

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 100,
        ]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'sale',
            'quantity_change' => -30,
        ]);

        $stockService = new StockService();
        $currentStock = $stockService->getCurrentStock($variant->id);

        $this->assertEquals(70, $currentStock);
    }

    public function test_stock_service_validates_availability(): void
    {
        $variant = ProductVariant::factory()->create();

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 50,
        ]);

        $stockService = new StockService();

        $this->assertTrue($stockService->validateStockAvailability($variant->id, 30));
    }

    public function test_stock_service_throws_exception_for_insufficient_stock(): void
    {
        $variant = ProductVariant::factory()->create();

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $stockService = new StockService();

        $this->expectException(\App\Exceptions\InsufficientStockException::class);
        $stockService->validateStockAvailability($variant->id, 50);
    }

    public function test_stock_service_creates_movement_on_deduction(): void
    {
        $variant = ProductVariant::factory()->create();

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 100,
        ]);

        $stockService = new StockService();
        $stockService->deductStockForOrder($variant->id, 25, 1);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'reference_type' => 'order',
            'reference_id' => 1,
            'movement_type' => 'sale',
            'quantity_change' => -25,
        ]);
    }

    public function test_stock_service_creates_movement_on_adjustment(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();

        $stockService = new StockService();
        $stockService->adjustStock($variant->id, 50, 'Manual adjustment', $user->id);

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'movement_type' => 'manual_adjustment',
            'quantity_change' => 50,
            'notes' => 'Manual adjustment',
            'created_by' => $user->id,
        ]);
    }

    public function test_stock_service_prevents_negative_stock(): void
    {
        $variant = ProductVariant::factory()->create();

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $stockService = new StockService();

        $this->expectException(\App\Exceptions\InsufficientStockException::class);
        $stockService->deductStockForOrder($variant->id, 50, 1);
    }

    public function test_order_service_generates_order_number(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 100,
        ]);

        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        $orderService = new OrderService(new StockService(), new ShippingService(), new PaymentService());
        $orderNumber = $orderService->generateOrderNumber();

        $this->assertStringStartsWith('ORD-', $orderNumber);
    }

    public function test_seo_service_generates_unique_slug(): void
    {
        Category::factory()->create(['slug' => 'test-category']);

        $seoService = new SeoService();
        $slug = $seoService->generateSlug('Test Category', Category::class);

        $this->assertStringStartsWith('test-category-', $slug);
    }

    public function test_shipping_service_creates_shipment(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->for($user)->create();

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-TEST-' . time(),
            'status' => 'paid',
            'shipping_name' => $address->full_name,
            'shipping_phone' => $address->phone,
            'shipping_address_line1' => $address->address_line1,
            'shipping_city' => $address->city,
            'shipping_state' => $address->state,
            'shipping_postal_code' => $address->postal_code,
            'shipping_country' => $address->country,
            'subtotal' => 100,
            'shipping_cost' => 10,
            'tax' => 10,
            'total' => 120,
        ]);

        $shippingService = app(\App\Services\ShippingService::class);
        $shipment = $shippingService->createShipmentForOrder($order);

        $this->assertNotNull($shipment);
        $this->assertEquals($order->id, $shipment->order_id);
    }

    public function test_shipping_service_calculates_shipping_cost(): void
    {
        $address = Address::factory()->create();

        $shippingService = new ShippingService();
        $cost = $shippingService->calculateShippingCost($address);

        $this->assertGreaterThan(0, $cost);
    }
}
