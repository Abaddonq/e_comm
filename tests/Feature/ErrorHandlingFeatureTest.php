<?php

namespace Tests\Feature;

use App\Exceptions\DomainException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\PaymentProcessingException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorHandlingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_insufficient_stock_exception_has_correct_properties(): void
    {
        $exception = new InsufficientStockException(
            'Not enough stock',
            1,
            10,
            5
        );

        $this->assertEquals(1, $exception->getVariantId());
        $this->assertEquals(10, $exception->getRequestedQuantity());
        $this->assertEquals(5, $exception->getAvailableQuantity());
        $this->assertEquals('insufficientstockexception', $exception->getErrorCode());
    }

    public function test_payment_processing_exception_has_correct_properties(): void
    {
        $exception = new PaymentProcessingException(
            'Payment declined',
            'order-123',
            ['error' => 'insufficient_funds']
        );

        $this->assertEquals('order-123', $exception->getOrderId());
        $this->assertEquals(['error' => 'insufficient_funds'], $exception->getGatewayResponse());
        $this->assertEquals('paymentprocessingexception', $exception->getErrorCode());
    }

    public function test_domain_exception_has_error_code(): void
    {
        $exception = new DomainException('Test error');
        
        $this->assertEquals('domainexception', $exception->getErrorCode());
    }

    public function test_custom_logging_channels_exist(): void
    {
        $this->assertTrue(config()->has('logging.channels.payment'));
        $this->assertTrue(config()->has('logging.channels.stock'));
        $this->assertTrue(config()->has('logging.channels.security'));
    }

    public function test_payment_logging_channel_is_daily(): void
    {
        $this->assertEquals('daily', config('logging.channels.payment.driver'));
    }

    public function test_stock_logging_channel_is_daily(): void
    {
        $this->assertEquals('daily', config('logging.channels.stock.driver'));
    }

    public function test_security_logging_channel_is_daily(): void
    {
        $this->assertEquals('daily', config('logging.channels.security.driver'));
    }

    public function test_custom_error_views_exist(): void
    {
        $this->assertTrue(file_exists(resource_path('views/errors/403.blade.php')));
        $this->assertTrue(file_exists(resource_path('views/errors/404.blade.php')));
        $this->assertTrue(file_exists(resource_path('views/errors/500.blade.php')));
    }
}
