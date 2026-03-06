<?php

namespace Tests\Feature;

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
}
