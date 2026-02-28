<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $query = $category->products()
            ->active()
            ->with(['category', 'variants', 'images']);

        if ($request->has('sort')) {
            match ($request->sort) {
                'price_asc' => $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')->orderBy('product_variants.price')->groupBy('products.id'),
                'price_desc' => $query->join('product_variants', 'products.id', '=', 'product_variants.product_id')->orderByDesc('product_variants.price')->groupBy('products.id'),
                'newest' => $query->orderBy('created_at', 'desc'),
                default => $query->orderBy('created_at', 'desc'),
            };
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20)->appends($request->query());

        return view('web.category', compact('category', 'products'));
    }
}
