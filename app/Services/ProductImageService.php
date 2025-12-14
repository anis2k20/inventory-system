<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProductImageService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Store the uploaded image file and create a thumbnail.
     */
    public function storeImage(UploadedFile $file): array
    {
        $originalPath = $file->store('products', 'public');

        // Create thumbnail
        $thumbnailPath = 'products/thumbnails/' . basename($originalPath);
        $image = ImageManager::gd()->read($file);
        $image->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        Storage::disk('public')->put($thumbnailPath, (string) $image->encode());

        return [
            'original' => $originalPath,
            'thumbnail' => $thumbnailPath,
        ];
    }

    /**
     * Delete the image file and thumbnail from storage.
     */
    public function deleteImage(array $paths): bool
    {
        $deleted = true;
        if (isset($paths['original']) && Storage::disk('public')->exists($paths['original'])) {
            $deleted &= Storage::disk('public')->delete($paths['original']);
        }
        if (isset($paths['thumbnail']) && Storage::disk('public')->exists($paths['thumbnail'])) {
            $deleted &= Storage::disk('public')->delete($paths['thumbnail']);
        }
        return $deleted;
    }
}
