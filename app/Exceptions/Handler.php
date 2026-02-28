<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (InsufficientStockException $e) {
            Log::channel('stock')->warning('Insufficient stock', [
                'variant_id' => $e->getVariantId(),
                'requested' => $e->getRequestedQuantity(),
                'available' => $e->getAvailableQuantity(),
                'message' => $e->getMessage(),
            ]);
        });

        $this->reportable(function (PaymentProcessingException $e) {
            Log::channel('payment')->error('Payment processing failed', [
                'order_id' => $e->getOrderId(),
                'message' => $e->getMessage(),
                'gateway_response' => $e->getGatewayResponse(),
            ]);
        });

        $this->reportable(function (PaymentVerificationException $e) {
            Log::channel('payment')->warning('Payment verification failed', [
                'callback_data' => $e->getCallbackData(),
                'message' => $e->getMessage(),
            ]);
        });

        $this->reportable(function (OrderCreationException $e) {
            Log::channel('payment')->error('Order creation failed', [
                'context' => $e->getContext(),
                'message' => $e->getMessage(),
            ]);
        });

        $this->reportable(function (DomainException $e) {
            Log::error('Domain exception occurred', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getErrorCode(),
            ]);
        });

        $this->reportable(function (Throwable $e) {
            if (app()->environment('production')) {
                Log::channel('security')->critical('Unhandled exception', [
                    'exception' => get_class($e),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    }
}
