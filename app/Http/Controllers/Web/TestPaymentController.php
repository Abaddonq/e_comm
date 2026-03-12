<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\StockMovement;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Support\OrderStatusMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestPaymentController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        PaymentService $paymentService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $apiKey = config('payment.iyzico.api_key');
        $baseUrl = config('payment.iyzico.base_url');
        
        $isConfigured = !empty($apiKey);
        $isSandbox = str_contains($baseUrl ?? '', 'sandbox');
        
        return view('web.test-payment', compact('isConfigured', 'isSandbox', 'baseUrl'));
    }

    public function createTestOrder(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'card_number' => 'required|string',
            'card_holder' => 'required|string',
            'expire_month' => 'required|string',
            'expire_year' => 'required|string',
            'cvv' => 'required|string',
        ]);

        $user = Auth::user();
        
        if (!$user) {
            $user = User::first();
            if (!$user) {
                $user = User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 'customer',
                ]);
            }
            Auth::login($user);
        }

        $category = Category::first();
        if (!$category) {
            $category = Category::factory()->create(['name' => 'Test Category']);
        }

        $product = Product::first();
        if (!$product) {
            $product = Product::factory()->for($category)->create([
                'title' => 'Test Product',
            ]);
        }

        $variant = ProductVariant::first();
        if (!$variant) {
            $variant = ProductVariant::factory()->for($product)->create([
                'price' => $validated['amount'],
                'sku' => 'TEST-' . rand(1000, 9999),
            ]);
            
            StockMovement::create([
                'product_variant_id' => $variant->id,
                'movement_type' => 'purchase',
                'quantity_change' => 100,
            ]);
        }

        $address = Address::first();
        if (!$address) {
            $address = Address::factory()->for($user)->create();
        }

        $cart = $this->cartService->getOrCreateCart($user->id);
        
        $existingItem = $cart->items()->where('product_variant_id', $variant->id)->first();
        if ($existingItem) {
            $existingItem->update(['quantity' => 1]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'quantity' => 1,
                'price' => $validated['amount'],
            ]);
        }

        $order = $this->orderService->createOrderFromCart(
            $cart,
            $address,
            'iyzico',
            $user->id
        );

        $order->update(['total' => $validated['amount']]);

        $customerData = [
            'email' => $user->email,
            'name' => $user->name,
            'phone' => $address->phone ?? '05555555555',
        ];

        $cardData = [
            'card_number' => $validated['card_number'],
            'card_holder_name' => $validated['card_holder'],
            'expire_month' => $validated['expire_month'],
            'expire_year' => $validated['expire_year'],
            'cvv' => $validated['cvv'],
        ];

        $paymentResult = $this->paymentService->initiatePayment($order, $customerData, $cardData);

        if ($paymentResult['success']) {
            $order->update([
                'status' => 'processing',
                'fulfillment_status' => OrderStatusMapper::FULFILLMENT_PROCESSING,
                'payment_status' => OrderStatusMapper::PAYMENT_PAID,
                'status_updated_at' => now(),
                'paid_at' => now(),
            ]);
            $order->payment->update(['status' => 'completed', 'paid_at' => now()]);
            
            return redirect()->route('checkout.success', ['orderId' => $order->id])
                ->with('success', 'Payment completed successfully!');
        }

        return back()->with('error', $paymentResult['error'] ?? 'Payment failed');
    }
}
