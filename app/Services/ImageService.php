<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    protected int $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function uploadProductImage(
        UploadedFile $file,
        int $productId,
        ?string $altText = null
        ): ProductImage
    {
        $this->validateImage($file);

        $filename = $this->generateFilename($file);
        $directory = "products/{$productId}";
        $path = "{$directory}/{$filename}";

        // Save the original file directly
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $image = ProductImage::create([
            'product_id' => $productId,
            'path' => $path,
            'alt_text' => $altText ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'is_primary' => false,
        ]);

        $this->generateThumbnails($image);

        return $image;
    }

    public function generateThumbnails(ProductImage $image): array
    {
        return [];
    }

    protected function resizeImage(
        string $originalPath,
        int $size,
        string $sizeName,
        int $productId
        ): ?string
    {
        try {
            $image = \Intervention\Image\ImageManager::gd()->read($originalPath);

            $image->cover($size, $size);

            $filename = pathinfo($originalPath, PATHINFO_FILENAME);
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newFilename = "{$filename}_{$sizeName}.webp";

            $directory = "products/{$productId}/thumbnails";
            $path = "{$directory}/{$newFilename}";

            Storage::disk('public')->put(
                $path,
                $image->toWebp(85)->toString()
            );

            return $path;
        }
        catch (\Exception $e) {
            return null;
        }
    }

    public function optimizeImage(string $path): bool
    {
        try {
            $image = \Intervention\Image\ImageManager::gd()->read(storage_path("app/public/{$path}"));

            $encoded = $image->toWebp(85);

            Storage::disk('public')->put($path, $encoded->toString());

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public function deleteImage(ProductImage $image): bool
    {
        try {
            Storage::disk('public')->delete($image->path);

            $thumbnailDirectory = dirname($image->path) . '/thumbnails';
            Storage::disk('public')->deleteDirectory($thumbnailDirectory);

            $image->delete();

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    protected function validateImage(UploadedFile $file): void
    {
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            throw new \InvalidArgumentException(
                'Invalid file type. Allowed types: JPEG, PNG, WebP'
                );
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new \InvalidArgumentException(
                'File too large. Maximum size: 5MB'
                );
        }
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $randomString = Str::random(8);

        return "{$randomString}.{$extension}";
    }

    public function getImageUrl(ProductImage $image, string $size = 'medium'): string
    {
        if ($size === 'original') {
            return asset("storage/{$image->path}");
        }

        $filename = pathinfo($image->path, PATHINFO_FILENAME);
        $thumbnailPath = dirname($image->path) . "/thumbnails/{$filename}_{$size}.webp";

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset("storage/{$thumbnailPath}");
        }

        return asset("storage/{$image->path}");
    }
}
