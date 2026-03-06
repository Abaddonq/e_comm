<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct(?PaymentGatewayInterface $gateway = null)
    {
        $this->gateway = $gateway ?? $this->resolveGateway();
    }

    protected function resolveGateway(): PaymentGatewayInterface
    {
        $gateway = config('payment.gateway', 'iyzico');

        return match ($gateway) {
                'stripe' => app(StripeGateway::class),
                default => app(IyzicoGateway::class),
            };
    }

    public function initiatePayment(Order $order, array $customerData, array $cardData = []): array
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_method' => config('payment.gateway', 'iyzico'),
            'amount' => $order->total,
            'currency' => 'TRY',
            'status' => 'pending',
        ]);

        $items = $order->items->map(function ($item) {
            return [
            'id' => $item->product_variant_id,
            'name' => $item->product_title,
            'category' => 'General',
            'price' => $item->unit_price,
            'quantity' => $item->quantity,
            ];
        })->toArray();

        $paymentData = [
            'order_id' => $order->id,
            'amount' => $order->total,
            'customer_email' => $customerData['email'],
            'customer_name' => $customerData['name'] ?? $order->shipping_name,
            'customer_phone' => $customerData['phone'] ?? $order->shipping_phone,
            'address' => $order->shipping_address_line1,
            'city' => $order->shipping_city,
            'postal_code' => $order->shipping_postal_code,
            'shipping_name' => $order->shipping_name,
            'items' => $items,
            'user_id' => $order->user_id,
            'ip' => request()->ip(),
        ];

        if (!empty($cardData)) {
            $paymentData = array_merge($paymentData, $cardData);
        }

        $result = $this->gateway->initiate($paymentData);

        if ($result['success']) {
            $payment->update([
                'transaction_id' => $result['transaction_id'] ?? null,
                'gateway_response' => $result,
            ]);
        }
        else {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $result['error'] ?? 'Payment initiation failed',
                'gateway_response' => $result,
            ]);
        }

        return $result;
    }

    public function verifyCallback(
        array $callbackData,
        ?string $rawPayload = null,
        ?string $signature = null,
        ?string $timestamp = null
    ): bool
    {
        return $this->gateway->verifyCallback($callbackData, $rawPayload, $signature, $timestamp);
    }

    public function processSuccessfulPayment(Order $order, array $callbackData): Payment
    {
        $transactionId = $this->gateway->getTransactionId($callbackData);

        $payment = $order->payment;
        $payment->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
            'gateway_response' => $callbackData,
        ]);

        $order->update([
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        Log::info('Payment processed successfully', [
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
        ]);

        return $payment;
    }

    public function processFailedPayment(Order $order, array $callbackData): Payment
    {
        $failureReason = $this->gateway->getFailureReason($callbackData);

        $payment = $order->payment;
        $payment->update([
            'status' => 'failed',
            'failure_reason' => $failureReason,
            'gateway_response' => $callbackData,
        ]);

        $order->update([
            'status' => 'cancelled',
        ]);

        Log::warning('Payment failed', [
            'order_id' => $order->id,
            'reason' => $failureReason,
        ]);

        return $payment;
    }

    public function getPaymentStatus(array $callbackData): string
    {
        return $this->gateway->getPaymentStatus($callbackData);
    }
}
