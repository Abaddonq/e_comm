<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected StockService $stockService;
    protected ShippingService $shippingService;
    protected PaymentService $paymentService;

    public function __construct(
        StockService $stockService,
        ShippingService $shippingService,
        PaymentService $paymentService
        )
    {
        $this->stockService = $stockService;
        $this->shippingService = $shippingService;
        $this->paymentService = $paymentService;
    }

    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        
        return DB::transaction(function () use ($date) {
            $prefix = "ORD-{$date}-";
            $maxLength = 20 - strlen($prefix);
            
            $lastOrder = Order::where('order_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('order_number', 'desc')
                ->first();
            
            $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -5) + 1 : 1;
            
            return sprintf('%s%05d', $prefix, $sequence);
        });
    }

    public function calculateOrderTotals(Cart $cart, ?Address $address = null): array
    {
        $subtotal = 0;

        foreach ($cart->items as $item) {
            $subtotal += $item->price * $item->quantity;
        }

        $shippingCost = $address ? $this->shippingService->calculateShippingCost($address) : 0;

        $taxRate = config('settings.tax_rate', 0);
        $tax = $subtotal * $taxRate;

        $total = $subtotal + $shippingCost + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    public function createOrderFromCart(
        Cart $cart,
        Address $address,
        string $paymentMethod,
        ?int $userId = null
        ): Order
    {
        return DB::transaction(function () use ($cart, $address, $paymentMethod, $userId) {
            $totals = $this->calculateOrderTotals($cart, $address);

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $totals['subtotal'],
                'shipping_cost' => $totals['shipping_cost'],
                'tax' => $totals['tax'],
                'total' => $totals['total'],
                'payment_method' => $paymentMethod,
                'shipping_name' => $address->full_name ?? 'Customer',
                'shipping_phone' => $address->phone ?? '',
                'shipping_address_line1' => $address->address_line1 ?? '',
                'shipping_address_line2' => $address->address_line2 ?? '',
                'shipping_city' => $address->city ?? '',
                'shipping_state' => $address->state ?? '',
                'shipping_postal_code' => $address->postal_code ?? '',
                'shipping_country' => $address->country ?? 'TR',
            ]);

            foreach ($cart->items as $item) {
                $variant = $item->variant;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_title' => $variant->product->title,
                    'variant_sku' => $variant->sku,
                    'variant_attributes' => $variant->attributes,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->price * $item->quantity,
                ]);

                $this->stockService->deductStockForOrder(
                    $variant->id,
                    $item->quantity,
                    $order->id
                );
            }

            $this->shippingService->createShipmentForOrder($order);

            $cart->items()->delete();

            Log::info('Order created', ['order_id' => $order->id, 'order_number' => $order->order_number]);

            // Dispatch order confirmation email
            \App\Jobs\SendOrderConfirmationEmail::dispatch($order);

            return $order;
        });
    }

    public function processPaymentCallback(int $orderId, array $callbackData): Order
    {
        $order = Order::findOrFail($orderId);

        $isValid = $this->paymentService->verifyCallback($callbackData);

        if (!$isValid) {
            Log::warning('Invalid payment callback', [
                'order_id' => $orderId,
                'callback_data' => $callbackData,
            ]);
            throw new \App\Exceptions\PaymentVerificationException('Invalid payment callback signature');
        }

        $paymentStatus = $this->paymentService->getPaymentStatus($callbackData);

        if ($paymentStatus === 'completed') {
            $order->update([
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            Log::info('Payment completed for order', ['order_id' => $order->id]);
        }
        else {
            $order->update([
                'status' => 'cancelled',
            ]);

            Log::warning('Payment failed for order', ['order_id' => $order->id]);
        }

        return $order->fresh();
    }

    public function cancelOrder(Order $order, string $reason): Order
    {
        return DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            foreach ($order->items as $item) {
                $this->stockService->restoreStockForCancellation(
                    $item->variant_id,
                    $item->quantity,
                    $order->id
                );
            }

            Log::info('Order cancelled', [
                'order_id' => $order->id,
                'reason' => $reason,
            ]);

            return $order->fresh();
        });
    }
}
