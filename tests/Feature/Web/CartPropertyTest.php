<?php

namespace Tests\Feature\Web;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CartPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function addStock(int $variantId, int $quantity): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        StockMovement::create([
            'product_variant_id' => $variantId,
            'reference' => 'initial_stock_for_test',
            'quantity_change' => $quantity,
            'reason' => 'initial_stock',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Property 13: Cart Operations Maintain Correct State
     * Validates: Requirements 5.3, 5.4, 5.7
     */
    public function test_cart_operations_maintain_correct_state(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100]);
        $this->addStock($variant->id, 10);

        $this->actingAs($user);

        // Add
        $response = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 2
        ]);
        $response->assertStatus(200);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals(1, $cart->items()->count());
        $item = $cart->items()->first();
        $this->assertEquals(2, $item->quantity);
        $this->assertEquals(100, $item->price);

        // Update
        $response = $this->postJson(route('cart.update'), [
            'item_id' => $item->id,
            'quantity' => 5
        ]);
        $response->assertStatus(200);
        $this->assertEquals(5, $item->fresh()->quantity);

        // Remove
        $response = $this->postJson(route('cart.remove'), [
            'item_id' => $item->id
        ]);
        $response->assertStatus(200);
        $this->assertEquals(0, $cart->items()->count());
    }

    /**
     * Property 14: Cart Association Matches User State
     * Validates: Requirements 5.2, 5.5
     */
    public function test_cart_association_matches_user_state(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $this->addStock($variant->id, 10);

        // Guest user
        $response = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 1
        ]);

        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();

        $this->assertNotNull($guestCart);
        $this->assertNull($guestCart->user_id);

        // Logged-in user
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 1
        ]);

        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);
        $this->assertNull($userCart->session_id);
    }

    /**
     * Property 15: Guest Login Merges Carts
     * Validates: Requirements 5.6
     */
    public function test_guest_login_merges_carts(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $this->addStock($variant->id, 10);

        // Start session as guest and add to cart
        $response = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 2
        ]);

        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();
        $this->assertNotNull($guestCart);
        $this->assertEquals(1, $guestCart->items()->count());

        // Login user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();

        // Assert guest cart is deleted and user cart has the item
        $this->assertNull(Cart::find($guestCart->id));

        $userCart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);
        $this->assertEquals(1, $userCart->items()->count());
        $this->assertEquals(2, $userCart->items()->first()->quantity);
    }

    /**
     * Property 16: Cart Displays Price Changes
     * Validates: Requirements 5.7
     */
    public function test_cart_displays_price_changes(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100]);
        $this->addStock($variant->id, 10);

        $this->actingAs($user);

        // Add to cart at 100
        $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 1
        ]);

        // Change price in DB to 120
        $variant->update(['price' => 120]);

        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);

        $cartData = $response->viewData('cartData');

        $this->assertTrue($cartData['items']->first()['price_changed']);
        $this->assertEquals(100, $cartData['items']->first()['original_price']);
        $this->assertEquals(120, $cartData['items']->first()['current_price']);
    }

    /**
     * Validates: Requirements 5.2
     */
    public function test_cart_validates_stock_on_add(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);
        $this->addStock($variant->id, 2);

        $this->actingAs($user);

        $response = $this->postJson(route('cart.add'), [
            'variant_id' => $variant->id,
            'quantity' => 5 // More than stock (2)
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Not enough stock available']);
    }
}
