<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_item_to_cart(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/cart/add', [
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    public function test_guest_can_add_item_to_cart(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 10,
        ]);

        $response = $this->postJson('/cart/add', [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_cannot_add_item_with_invalid_variant(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/cart/add', [
            'variant_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
    }

    public function test_cart_displays_items_with_details(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 100]);

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'price' => 100,
        ]);

        $response = $this->actingAs($user)->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('200.00');
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 50,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/cart/update', [
            'item_id' => $cartItem->id,
            'quantity' => 5,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $cart = Cart::create(['user_id' => $user->id]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 50,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/cart/remove', [
            'item_id' => $cartItem->id,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }
}
