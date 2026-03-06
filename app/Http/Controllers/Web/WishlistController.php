<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Favorilere eklemek için giriş yapmalısınız'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $isAdded = false;
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            $isAdded = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'is_added' => $isAdded,
            'wishlist_count' => $count
        ]);
    }

    public function check(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['is_wishlisted' => false]);
        }

        $isWishlisted = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        return response()->json(['is_wishlisted' => $isWishlisted]);
    }

    public function count(): JsonResponse
    {
        $count = auth()->check() 
            ? Wishlist::where('user_id', auth()->id())->count()
            : 0;

        return response()->json(['count' => $count]);
    }
}
