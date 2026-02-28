<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeGateway implements PaymentGatewayInterface
{
    protected string $secretKey;
    protected string $publishableKey;
    protected bool $testMode;

    public function __construct()
    {
        $this->secretKey = config('payment.stripe.api_key');
        $this->publishableKey = config('payment.stripe.webhook_secret');
        $this->testMode = true;
    }

    public function initiate(array $paymentData): array
    {
        try {
            Stripe::setApiKey($this->secretKey);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $this->formatLineItems($paymentData['items'] ?? []),
                'mode' => 'payment',
                'success_url' => $paymentData['success_url'],
                'cancel_url' => $paymentData['cancel_url'],
                'customer_email' => $paymentData['customer_email'] ?? null,
                'metadata' => [
                    'order_id' => $paymentData['order_id'],
                    'user_id' => $paymentData['user_id'] ?? null,
                ],
            ]);

            return [
                'success' => true,
                'payment_url' => $session->url,
                'transaction_id' => $session->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment initiation failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function verifyCallback(array $callbackData): bool
    {
        $eventId = $callbackData['event_id'] ?? null;
        
        if (!$eventId) {
            return false;
        }

        try {
            Stripe::setApiKey($this->secretKey);
            $event = \Stripe\Event::retrieve($eventId, $this->getStripeOptions());
            
            return in_array($event->type, [
                'checkout.session.completed',
                'payment_intent.succeeded',
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe callback verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function getTransactionId(array $callbackData): ?string
    {
        return $callbackData['payment_intent'] ?? $callbackData['session_id'] ?? null;
    }

    public function getPaymentStatus(array $callbackData): string
    {
        if (isset($callbackData['status'])) {
            return match ($callbackData['status']) {
                'succeeded', 'complete' => 'completed',
                'failed' => 'failed',
                'pending' => 'pending',
                default => 'pending',
            };
        }

        return 'pending';
    }

    public function getFailureReason(array $callbackData): ?string
    {
        return $callbackData['error_message'] ?? $callbackData['failure_reason'] ?? null;
    }

    protected function formatLineItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'try',
                    'product_data' => [
                        'name' => $item['name'] ?? 'Product',
                    ],
                    'unit_amount' => (int) ($item['price'] * 100),
                ],
                'quantity' => $item['quantity'] ?? 1,
            ];
        }, $items);
    }

    protected function getStripeOptions(): array
    {
        return [
            'stripe_account' => null,
        ];
    }
}
