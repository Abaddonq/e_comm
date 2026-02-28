<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo e(route('home')); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    @foreach($categories as $category)
    <url>
        <loc><?php echo e(route('category.show', $category->slug)); ?></loc>
        <lastmod><?php echo e($category->updated_at->toIso8601String()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
    
    @foreach($products as $product)
    <url>
        <loc><?php echo e(route('product.show', $product->slug)); ?></loc>
        <lastmod><?php echo e($product->updated_at->toIso8601String()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
</urlset>
