<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Address;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use App\Support\OrderStatusMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected OrderService $orderService;
    protected PaymentService $paymentService;
    protected ShippingService $shippingService;

    public function __construct(
        CartService $cartService,
        OrderService $orderService,
        PaymentService $paymentService,
        ShippingService $shippingService
    ) {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
        $this->shippingService = $shippingService;
    }

    public function index(Request $request)
    {
        $cart = $this->getCart($request);
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $stockErrors = $this->cartService->validateStock($cart);
        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', 'Some items in your cart are no longer available.');
        }

        $cartData = $this->cartService->calculateTotal($cart);
        
        $addresses = Auth::check() 
            ? Auth::user()->addresses()->get() 
            : collect([]);

        return view('web.checkout.index', compact('cart', 'cartData', 'addresses'));
    }

    public function process(CheckoutRequest $request)
    {
        $cart = $this->getCart($request);
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $address = Address::findOrFail($request->address_id);
        
        if (Auth::check() && $address->user_id !== Auth::id()) {
            return back()->withErrors(['address_id' => 'Invalid address selected.']);
        }

        $stockErrors = $this->cartService->validateStock($cart);
        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', 'Some items are no longer available.');
        }

        $order = $this->orderService->createOrderFromCart(
            $cart,
            $address,
            $request->payment_method,
            Auth::id()
        );

        $customerData = [
            'email' => Auth::check() ? Auth::user()->email : ($address->email ?? 'guest@example.com'),
            'name' => $address->full_name,
            'phone' => $address->phone,
        ];

        $cardData = [];
        if ($request->payment_method === 'iyzico') {
            $cardData = [
                'card_number' => $request->card_number,
                'card_holder_name' => $request->card_holder,
                'expire_month' => $request->expire_month,
                'expire_year' => $request->expire_year,
                'cvv' => $request->cvv,
            ];
        }

        $paymentResult = $this->paymentService->initiatePayment($order, $customerData, $cardData);

        if (!$paymentResult['success']) {
            $order->update([
                'status' => 'cancelled',
                'fulfillment_status' => OrderStatusMapper::FULFILLMENT_CANCELLED,
                'payment_status' => OrderStatusMapper::PAYMENT_FAILED,
                'status_updated_at' => now(),
            ]);
            return back()->withErrors(['payment' => $paymentResult['error'] ?? 'Payment initiation failed.']);
        }

        if (isset($paymentResult['payment_url'])) {
            return redirect($paymentResult['payment_url']);
        }

        // For direct payment (no redirect), update status immediately
        $order->update([
            'status' => 'processing',
            'fulfillment_status' => OrderStatusMapper::FULFILLMENT_PROCESSING,
            'payment_status' => OrderStatusMapper::PAYMENT_PAID,
            'status_updated_at' => now(),
            'paid_at' => now(),
        ]);
        $order->payment->update(['status' => 'completed', 'paid_at' => now()]);

        return redirect()->route('checkout.success', $order->id);
    }

    public function success(Request $request, int $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('web.checkout.success', compact('order'));
    }

    public function cancel(Request $request, int $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);

        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $order->update([
            'status' => 'cancelled',
            'fulfillment_status' => OrderStatusMapper::FULFILLMENT_CANCELLED,
            'payment_status' => OrderStatusMapper::PAYMENT_CANCELLED,
            'status_updated_at' => now(),
        ]);

        return redirect()->route('cart.index')->with('error', 'Payment was cancelled.');
    }

    protected function getCart(Request $request): ?Cart
    {
        if (Auth::check()) {
            return $this->cartService->getOrCreateCart(Auth::id());
        }

        if ($request->session()->has('cart_id')) {
            return Cart::find($request->session()->get('cart_id'));
        }

        return null;
    }
}
