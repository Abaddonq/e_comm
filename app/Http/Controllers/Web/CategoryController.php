<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $query = $category->products()
            ->active()
            ->select('products.*')
            ->with(['category', 'variants', 'images']);

        // Price filter
        if ($request->has('min_price') || $request->has('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                if ($request->min_price) {
                    $q->where('price', '>=', (float) $request->min_price);
                }
                if ($request->max_price) {
                    $q->where('price', '<=', (float) $request->max_price);
                }
            });
        }

        // In stock filter
        if ($request->has('in_stock') && $request->in_stock == 1) {
            $query->whereHas('variants', function ($q) {
                $q->where('current_stock', '>', 0);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->withMin('variants', 'price')
                ->orderBy('variants_min_price'),
            'price_desc' => $query->withMin('variants', 'price')
                ->orderByDesc('variants_min_price'),
            'name_asc' => $query->orderBy('title', 'asc'),
            'name_desc' => $query->orderBy('title', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12)->appends($request->except('page'));

        // Get price range for filters
        $priceRange = ProductVariant::whereHas('product', function ($q) use ($category) {
            $q->where('category_id', $category->id)->where('is_active', true);
        })->select(DB::raw('MIN(price) as min_price, MAX(price) as max_price'))->first();

        // Get wishlist product IDs for authenticated user
        $wishlistProductIds = [];
        if (auth()->check()) {
            $wishlistProductIds = Wishlist::where('user_id', auth()->id())
                ->pluck('product_id')
                ->toArray();
        }

        return view('web.category', compact('category', 'products', 'priceRange', 'wishlistProductIds'));
    }
}
