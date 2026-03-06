<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function handleCallback(Request $request)
    {
        $rawPayload = $request->getContent();
        $signature = (string) ($request->header('X-Iyzico-Signature')
            ?? $request->header('X-Stripe-Signature')
            ?? $request->header('X-Signature')
            ?? '');
        $timestamp = $request->header('X-Iyzico-Timestamp')
            ?? $request->header('X-Timestamp')
            ?? null;

        $callbackData = $request->all();

        Log::info('Payment callback received', [
            'has_signature' => $signature !== '',
            'has_timestamp' => $timestamp !== null,
            'conversation_id' => $callbackData['conversationId'] ?? null,
            'order_id' => $callbackData['order_id'] ?? null,
            'gateway' => config('payment.gateway', 'iyzico'),
        ]);

        if (empty($callbackData)) {
            Log::warning('Empty payment callback received');
            return response()->json(['error' => 'No data provided'], 400);
        }

        $orderId = $callbackData['conversationId'] ?? $callbackData['order_id'] ?? null;

        if (!$orderId) {
            Log::warning('Payment callback missing order ID', [
                'has_signature' => $signature !== '',
                'has_timestamp' => $timestamp !== null,
            ]);
            return response()->json(['error' => 'Order ID not found'], 400);
        }

        try {
            $order = Order::findOrFail($orderId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found for payment callback', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        try {
            if ($signature !== '') {
                $cacheKey = 'webhook_sig:' . hash('sha256', $signature);
                $added = Cache::add($cacheKey, true, now()->addMinutes(10));

                if (!$added) {
                    Log::warning('Duplicate payment callback blocked', ['order_id' => $orderId]);
                    return response()->json(['error' => 'Duplicate callback'], 409);
                }
            }

            $isValid = $this->paymentService->verifyCallback($callbackData, $rawPayload, $signature, $timestamp);

            if (!$isValid) {
                Log::warning('Invalid payment callback signature', [
                    'order_id' => $orderId,
                    'has_signature' => $signature !== '',
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $status = $this->paymentService->getPaymentStatus($callbackData);

            if ($status === 'completed') {
                $this->paymentService->processSuccessfulPayment($order, $callbackData);
                Log::info('Payment successful', ['order_id' => $orderId]);
            } else {
                $this->paymentService->processFailedPayment($order, $callbackData);
                Log::warning('Payment failed', ['order_id' => $orderId, 'status' => $status]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Payment callback processing error', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing error'], 500);
        }
    }
}
