<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cart = null;
        $cartData = ['items' => [], 'subtotal' => 0, 'item_count' => 0];

        if (auth()->check()) {
            $cart = $this->cartService->getOrCreateCart(auth()->id());
            $cartData = $this->cartService->calculateTotal($cart);
        }
        elseif ($request->session()->has('cart_id')) {
            $cart = Cart::find($request->session()->get('cart_id'));
            if ($cart) {
                $cartData = $this->cartService->calculateTotal($cart);
            }
        }

        return view('web.cart', compact('cart', 'cartData'));
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);

        if (!$variant->is_active || !$variant->product->is_active) {
            return response()->json(['error' => 'Product is not available'], 400);
        }

        if ($variant->current_stock < $request->quantity) {
            return response()->json(['error' => 'Not enough stock available'], 400);
        }

        if (auth()->check()) {
            $cart = $this->cartService->getOrCreateCart(auth()->id());
        }
        else {
            $sessionId = session()->getId();
            $cart = $this->cartService->getOrCreateCart(null, $sessionId);
            session()->put('cart_id', $cart->id);
        }

        $item = $this->cartService->addItem($cart, $request->variant_id, $request->quantity);

        $cartData = $this->cartService->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => $cartData['item_count'],
        ]);
    }

    public function update(\App\Http\Requests\Web\UpdateCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cartItem = \App\Models\CartItem::findOrFail($validated['item_id']);

        if (auth()->check() && $cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->cartService->updateItemQuantity($cartItem, $validated['quantity']);

        $cart = $cartItem->cart;
        $cartData = $this->cartService->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'cart_count' => $cartData['item_count'],
            'subtotal' => $cartData['subtotal'],
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = \App\Models\CartItem::findOrFail($request->item_id);

        if (auth()->check() && $cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->cartService->removeItem($cartItem);

        $cart = $cartItem->cart;
        $cartData = $this->cartService->calculateTotal($cart);

        return response()->json([
            'success' => true,
            'cart_count' => $cartData['item_count'],
            'subtotal' => $cartData['subtotal'],
        ]);
    }
}
