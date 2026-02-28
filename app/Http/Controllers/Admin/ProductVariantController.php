<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'attributes' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'initial_stock' => 'nullable|integer|min:0',
        ]);

        $variant = $product->variants()->create($validated);

        if (!empty($validated['initial_stock'])) {
            $this->stockService->adjustStock(
                $variant->id,
                $validated['initial_stock'],
                'Initial stock',
                auth()->id()
            );
        }

        return back()->with('success', 'Variant added successfully.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255|unique:product_variants,sku,' . $variant->id,
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'attributes' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $variant->update($validated);

        return back()->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        if ($variant->orderItems()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete variant with existing orders.']);
        }

        $variant->delete();

        return back()->with('success', 'Variant deleted successfully.');
    }
}
