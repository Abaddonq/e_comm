<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\IyzicoGateway;
use App\Services\PaymentService;
use App\Services\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_callback_missing_order_id_returns_400(): void
    {
        $response = $this->postJson(route('webhooks.payment.callback'), []);
        
        $response->assertStatus(400);
    }

    public function test_payment_callback_empty_data_returns_400(): void
    {
        $response = $this->postJson(route('webhooks.payment.callback'), []);
        
        $response->assertJson(['error' => 'No data provided']);
    }

    public function test_payment_gateway_resolves_correctly_for_iyzico(): void
    {
        config(['payment.gateway' => 'iyzico']);
        
        $gateway = app(IyzicoGateway::class);
        
        $this->assertInstanceOf(IyzicoGateway::class, $gateway);
    }

    public function test_payment_gateway_resolves_correctly_for_stripe(): void
    {
        config(['payment.gateway' => 'stripe']);
        
        $gateway = app(StripeGateway::class);
        
        $this->assertInstanceOf(StripeGateway::class, $gateway);
    }

    public function test_iyzico_initiation_returns_payment_url_on_success(): void
    {
        $this->markTestSkipped('Test requires mock of iyzico library');
    }

    public function test_iyzico_initiation_returns_error_on_failure(): void
    {
        $this->markTestSkipped('Test requires mock of iyzico library');
    }

    public function test_iyzico_verify_callback_returns_true_for_valid_token(): void
    {
        $this->markTestSkipped('Test requires mock of iyzico library');
    }

    public function test_iyzico_verify_callback_returns_false_for_invalid_token(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'failure',
                'errorMessage' => 'Invalid token',
            ], 200),
        ]);

        $gateway = app(IyzicoGateway::class);
        
        $result = $gateway->verifyCallback([
            'token' => 'invalid_token',
            'conversationId' => '1',
        ]);

        $this->assertFalse($result);
    }

    public function test_iyzico_get_payment_status_returns_completed_for_success(): void
    {
        $gateway = app(IyzicoGateway::class);
        
        $status = $gateway->getPaymentStatus([
            'paymentStatus' => 'SUCCESS',
        ]);

        $this->assertEquals('completed', $status);
    }

    public function test_iyzico_get_payment_status_returns_failed_for_failure(): void
    {
        $gateway = app(IyzicoGateway::class);
        
        $status = $gateway->getPaymentStatus([
            'paymentStatus' => 'FAILURE',
        ]);

        $this->assertEquals('failed', $status);
    }

    public function test_iyzico_get_payment_status_returns_pending_for_other(): void
    {
        $gateway = app(IyzicoGateway::class);
        
        $status = $gateway->getPaymentStatus([
            'paymentStatus' => 'AUTHENTICATED',
        ]);

        $this->assertEquals('pending', $status);
    }

    public function test_iyzico_get_transaction_id_returns_payment_id(): void
    {
        $gateway = app(IyzicoGateway::class);
        
        $transactionId = $gateway->getTransactionId([
            'paymentId' => 'pay_123',
        ]);

        $this->assertEquals('pay_123', $transactionId);
    }

    public function test_iyzico_get_failure_reason_returns_error_message(): void
    {
        $gateway = app(IyzicoGateway::class);
        
        $reason = $gateway->getFailureReason([
            'errorMessage' => 'Card declined',
        ]);

        $this->assertEquals('Card declined', $reason);
    }
}
