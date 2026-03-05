<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|file|mimetypes:image/avif,image/webp|max:5120',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $uploadedImages = [];

        foreach ($validated['images'] as $image) {
            try {
                $productImage = $this->imageService->uploadProductImage(
                    $image,
                    $product->id,
                    $validated['alt_text'] ?? null
                );

                $uploadedImages[] = $productImage;
            } catch (\Exception $e) {
                return back()->withErrors(['images' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        if ($product->images()->count() === 1) {
            $productImage = $product->images()->first();
            if ($productImage && !$productImage->is_primary) {
                $productImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', count($uploadedImages) . ' image(s) uploaded successfully.');
    }

    public function destroy(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return back()->withErrors(['delete' => 'Image does not belong to this product.']);
        }

        $this->imageService->deleteImage($image);

        if ($image->is_primary && $product->images()->count() > 0) {
            $newPrimary = $product->images()->first();
            $newPrimary?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    public function setPrimary(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return back()->withErrors(['update' => 'Image does not belong to this product.']);
        }

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    public function reorder(Request $request, Product $product)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|integer|exists:product_images,id',
        ]);

        foreach ($validated['images'] as $index => $imageId) {
            ProductImage::where('id', $imageId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Image order updated.');
    }
}
