<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(?int $userId = null, ?string $sessionId = null): Cart
    {
        if ($userId) {
            $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['session_id' => null]
            );
        }
        elseif ($sessionId) {
            $cart = Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
            );
        }
        else {
            throw new \InvalidArgumentException('Either userId or sessionId must be provided');
        }

        return $cart;
    }

    public function addItem(Cart $cart, int $variantId, int $quantity = 1): CartItem
    {
        $variant = ProductVariant::findOrFail($variantId);

        $currentStock = $variant->current_stock;
        if ($currentStock < $quantity) {
            throw new \App\Exceptions\InsufficientStockException(
                "Insufficient stock for variant {$variantId}. Requested: {$quantity}, Available: {$currentStock}",
                $variantId,
                $quantity,
                $currentStock
                );
        }

        $existingItem = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($currentStock < $newQuantity) {
                throw new \App\Exceptions\InsufficientStockException(
                    "Insufficient stock for variant {$variantId}. Requested: {$newQuantity} (total), Available: {$currentStock}",
                    $variantId,
                    $newQuantity,
                    $currentStock
                    );
            }
            $existingItem->quantity = $newQuantity;
            $existingItem->save();
            return $existingItem;
        }

        return $cart->items()->create([
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
            'price' => $variant->price,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            $item->delete();
            return $item;
        }

        $item->quantity = $quantity;
        $item->save();

        return $item;
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function mergeCarts(Cart $sourceCart, Cart $targetCart): void
    {
        DB::transaction(function () use ($sourceCart, $targetCart) {
            foreach ($sourceCart->items as $sourceItem) {
                $targetItem = $targetCart->items()
                    ->where('product_variant_id', $sourceItem->product_variant_id)
                    ->first();

                if ($targetItem) {
                    $targetItem->quantity += $sourceItem->quantity;
                    $targetItem->save();
                }
                else {
                    $targetCart->items()->create([
                        'product_variant_id' => $sourceItem->product_variant_id,
                        'quantity' => $sourceItem->quantity,
                        'price' => $sourceItem->price,
                    ]);
                }
            }

            $sourceCart->items()->delete();
            $sourceCart->delete();
        });
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function calculateTotal(Cart $cart): array
    {
        $items = $cart->items->map(function ($item) {
            $currentPrice = $item->variant->price;
            $priceChanged = $currentPrice != $item->price;

            return [
            'item' => $item,
            'subtotal' => $currentPrice * $item->quantity,
            'price_changed' => $priceChanged,
            'original_price' => $item->price,
            'current_price' => $currentPrice,
            ];
        });

        $subtotal = $items->sum('subtotal');

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'item_count' => $cart->items->sum('quantity'),
        ];
    }

    public function validateStock(Cart $cart): array
    {
        $errors = [];

        foreach ($cart->items as $item) {
            $availableStock = $item->variant->current_stock;

            if ($availableStock < $item->quantity) {
                $errors[] = [
                    'variant_id' => $item->variant_id,
                    'product' => $item->variant->product->title,
                    'sku' => $item->variant->sku,
                    'requested' => $item->quantity,
                    'available' => $availableStock,
                ];
            }
        }

        return $errors;
    }

    public function getCartCount(?int $userId = null, ?string $sessionId = null): int
    {
        $query = Cart::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        } else {
            return 0;
        }

        $cart = $query->with('items')->first();
        
        if (!$cart) {
            return 0;
        }

        return $cart->items->sum('quantity');
    }
}
