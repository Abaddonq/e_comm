<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['category', 'variants', 'images'])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'variants', 'images'])
            ->limit(4)
            ->get();

        return view('web.product', compact('product', 'relatedProducts'));
    }
}
