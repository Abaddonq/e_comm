<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $query = ProductVariant::with(['product', 'stockMovements'])
            ->whereHas('product');

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('sku', 'like', '%' . $request->search . '%')
                    ->orWhereHas('product', function ($pq) use ($request) {
                        $pq->where('title', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereRaw('(
                SELECT COALESCE(SUM(quantity_change), 0)
                FROM stock_movements
                WHERE stock_movements.variant_id = product_variants.id
            ) < 10');
        }

        $variants = $query->paginate(50)->appends($request->query());

        $variants->getCollection()->transform(function ($variant) {
            $variant->current_stock = $this->stockService->getCurrentStock($variant->id);
            return $variant;
        });

        return view('admin.stock.index', compact('variants'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'adjustments' => 'required|array|min:1',
            'adjustments.*.variant_id' => 'required|exists:product_variants,id',
            'adjustments.*.quantity' => 'required|integer',
            'adjustments.*.reason' => 'required|string|max:255',
        ]);

        $results = [];

        foreach ($validated['adjustments'] as $adjustment) {
            $variant = ProductVariant::find($adjustment['variant_id']);

            $currentStock = $this->stockService->getCurrentStock($variant->id);
            $newStock = $currentStock + $adjustment['quantity'];

            if ($newStock < 0) {
                $results[] = [
                    'variant_id' => $variant->id,
                    'sku' => $variant->sku,
                    'success' => false,
                    'message' => "Cannot reduce stock. Current: {$currentStock}, Requested reduction: " . abs($adjustment['quantity']),
                ];
                continue;
            }

            $this->stockService->adjustStock(
                $variant->id,
                $adjustment['quantity'],
                $adjustment['reason'],
                auth()->id()
            );

            $results[] = [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'success' => true,
                'message' => "Stock adjusted from {$currentStock} to {$newStock}",
            ];
        }

        $failures = array_filter($results, fn($r) => !$r['success']);

        if (count($failures) > 0) {
            return back()->withErrors($failures);
        }

        return back()->with('success', 'Stock adjusted successfully.');
    }

    public function history(ProductVariant $variant)
    {
        $history = StockMovement::with('creator')
            ->where('product_variant_id', $variant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $currentStock = $this->stockService->getCurrentStock($variant->id);

        return view('admin.stock.history', compact('variant', 'history', 'currentStock'));
    }
}
