<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_test_payment_page(): void
    {
        $response = $this->get('/test-payment');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_test_payment_page(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get('/test-payment');

        $response->assertStatus(403);
    }

    public function test_webhook_rejects_unsigned_callback_when_strict_mode_enabled(): void
    {
        config([
            'payment.gateway' => 'iyzico',
            'payment.iyzico.webhook_secret' => 'test_webhook_secret',
            'payment.iyzico.webhook_strict' => true,
        ]);

        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->postJson('/webhooks/payment/callback', [
            'conversationId' => (string) $order->id,
            'paymentStatus' => 'SUCCESS',
        ]);

        $response->assertStatus(400);
    }

    public function test_authenticated_user_cannot_cancel_another_user_order_via_checkout_cancel_route(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $orderB = Order::factory()->create([
            'user_id' => $userB->id,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($userA)->get(route('checkout.cancel', $orderB->id));

        $response->assertStatus(403);
        $this->assertSame('paid', $orderB->fresh()->status);
    }

    public function test_owner_can_cancel_own_order_via_checkout_cancel_route(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)->get(route('checkout.cancel', $order->id));

        $response->assertRedirect(route('cart.index'));
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_authenticated_user_cannot_update_another_users_cart_item(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $userB->id]);
        $cartItem = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->actingAs($userA)->post(route('cart.update'), [
            'item_id' => $cartItem->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_update_guest_cart_item_from_another_session(): void
    {
        $cart = Cart::factory()->guest()->create();
        $cartItem = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->post(route('cart.update'), [
            'item_id' => $cartItem->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_cannot_remove_another_users_cart_item(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $userB->id]);
        $cartItem = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->withoutMiddleware(VerifyCsrfToken::class);

        $response = $this->actingAs($userA)->post(route('cart.remove'), [
            'item_id' => $cartItem->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_layout_script_no_longer_uses_unsafe_innerhtml_patterns(): void
    {
        $contents = file_get_contents(resource_path('views/layouts/web.blade.php'));

        $this->assertStringNotContainsString('toast.innerHTML = `', $contents);
        $this->assertStringNotContainsString('recentList.innerHTML = recent.map', $contents);
        $this->assertStringNotContainsString('suggestionsList.innerHTML = data.products.map', $contents);
        $this->assertStringContainsString('function clearElement(el)', $contents);
    }
}
