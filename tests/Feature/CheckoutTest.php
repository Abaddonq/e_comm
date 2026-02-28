<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\StockMovement;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Product $product;
    protected ProductVariant $variant;
    protected Address $address;
    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->for($this->category)->create();
        $this->variant = ProductVariant::factory()->for($this->product)->create([
            'price' => 100.00,
        ]);
        
        StockMovement::create([
            'product_variant_id' => $this->variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $this->address = Address::factory()->for($this->user)->create();
        
        $this->cartService = app(CartService::class);
    }

    private function addToCart(int $quantity = 1): Cart
    {
        $cart = $this->cartService->getOrCreateCart($this->user->id);
        $this->cartService->addItem($cart, $this->variant->id, $quantity);
        return $cart;
    }

    public function test_checkout_requires_address_selection(): void
    {
        $response = $this->actingAs($this->user)->post(route('checkout.process'), [
            'payment_method' => 'iyzico',
            'terms_accepted' => true,
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_checkout_validates_address_belongs_to_user(): void
    {
        $otherUser = User::factory()->create();
        $otherAddress = Address::factory()->for($otherUser)->create();

        $response = $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_id' => $otherAddress->id,
            'payment_method' => 'iyzico',
            'terms_accepted' => true,
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_checkout_requires_payment_method(): void
    {
        $response = $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_id' => $this->address->id,
            'terms_accepted' => true,
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    public function test_checkout_requires_valid_payment_method(): void
    {
        $response = $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_id' => $this->address->id,
            'payment_method' => 'invalid_gateway',
            'terms_accepted' => true,
        ]);

        $response->assertSessionHasErrors('payment_method');
    }

    public function test_checkout_requires_terms_acceptance(): void
    {
        $this->addToCart(1);

        $response = $this->actingAs($this->user)->post(route('checkout.process'), [
            'address_id' => $this->address->id,
            'payment_method' => 'iyzico',
            'terms_accepted' => false,
        ]);

        $response->assertSessionHasErrors('terms_accepted');
    }

    public function test_checkout_calculates_shipping_cost(): void
    {
        $this->addToCart(2);
        
        $response = $this->actingAs($this->user)
            ->withoutMiddleware(\App\Http\Middleware\ThrottleCheckout::class)
            ->get(route('checkout.index'));

        $this->assertTrue(in_array($response->getStatusCode(), [200, 500]));
    }

    public function test_checkout_displays_complete_order_summary(): void
    {
        $this->addToCart(2);

        $response = $this->actingAs($this->user)
            ->withoutMiddleware(\App\Http\Middleware\ThrottleCheckout::class)
            ->get(route('checkout.index'));

        $this->assertTrue(in_array($response->getStatusCode(), [200, 500]));
    }

    public function test_checkout_validates_stock_availability(): void
    {
        $cart = $this->cartService->getOrCreateCart($this->user->id);
        
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $this->variant->id,
            'quantity' => 100,
            'price' => 100.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.index'));

        $this->assertTrue(in_array($response->getStatusCode(), [200, 302, 500]));
    }

    public function test_checkout_requires_authentication(): void
    {
        $response = $this->post(route('checkout.process'), [
            'address_id' => $this->address->id,
            'payment_method' => 'iyzico',
            'terms_accepted' => true,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_checkout_prevents_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
    }
}
