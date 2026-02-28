<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Redirect;

class ProductObserver
{
    public function updating(Product $product): void
    {
        if ($product->isDirty('slug') && $product->getOriginal('slug')) {
            $oldSlug = $product->getOriginal('slug');
            $newSlug = $product->slug;
            
            Redirect::updateOrCreate(
                ['old_path' => '/products/' . $oldSlug],
                [
                    'new_path' => '/products/' . $newSlug,
                    'status_code' => 301,
                ]
            );
        }
    }
}
