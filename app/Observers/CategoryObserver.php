<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Redirect;

class CategoryObserver
{
    public function updating(Category $category): void
    {
        if ($category->isDirty('slug') && $category->getOriginal('slug')) {
            $oldSlug = $category->getOriginal('slug');
            $newSlug = $category->slug;
            
            Redirect::updateOrCreate(
                ['old_path' => '/categories/' . $oldSlug],
                [
                    'new_path' => '/categories/' . $newSlug,
                    'status_code' => 301,
                ]
            );
        }
    }
}
