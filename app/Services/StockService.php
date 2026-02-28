<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Collection;

class StockService
{
    public function getCurrentStock(int $variantId): int
    {
        return StockMovement::where('product_variant_id', $variantId)
            ->sum('quantity_change');
    }

    public function deductStockForOrder(int $variantId, int $quantity, int $orderId): StockMovement
    {
        $this->validateStockAvailability($variantId, $quantity);

        return StockMovement::create([
            'product_variant_id' => $variantId,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'movement_type' => 'sale',
            'quantity_change' => -$quantity,
        ]);
    }

    public function restoreStockForCancellation(int $variantId, int $quantity, int $orderId): StockMovement
    {
        return StockMovement::create([
            'product_variant_id' => $variantId,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'movement_type' => 'cancellation',
            'quantity_change' => $quantity,
        ]);
    }

    public function adjustStock(
        int $variantId,
        int $quantity,
        string $reason,
        ?int $userId = null,
        ?int $orderId = null
    ): StockMovement {
        return StockMovement::create([
            'product_variant_id' => $variantId,
            'reference_type' => $orderId ? 'order' : null,
            'reference_id' => $orderId,
            'created_by' => $userId,
            'movement_type' => 'manual_adjustment',
            'quantity_change' => $quantity,
            'notes' => $reason,
        ]);
    }

    public function getStockHistory(int $variantId): Collection
    {
        return StockMovement::where('product_variant_id', $variantId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function validateStockAvailability(int $variantId, int $quantity): bool
    {
        $currentStock = $this->getCurrentStock($variantId);

        if ($currentStock < $quantity) {
            throw new \App\Exceptions\InsufficientStockException(
                "Insufficient stock for variant {$variantId}. Available: {$currentStock}, Requested: {$quantity}"
            );
        }

        return true;
    }

    public function addPurchaseStock(
        int $variantId,
        int $quantity,
        string $reference = null
    ): StockMovement {
        return StockMovement::create([
            'product_variant_id' => $variantId,
            'movement_type' => 'purchase',
            'quantity_change' => $quantity,
            'notes' => $reference ?? 'Purchase',
        ]);
    }

    public function addRefundStock(
        int $variantId,
        int $quantity,
        int $orderId,
        string $reason = null
    ): StockMovement {
        return StockMovement::create([
            'product_variant_id' => $variantId,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'movement_type' => 'refund',
            'quantity_change' => $quantity,
            'notes' => $reason ?? "Order #{$orderId} - Refund",
        ]);
    }
}
