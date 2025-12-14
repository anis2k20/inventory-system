<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ProductImageService
{
    public function __construct()
    {
    }

    public function storeImage(UploadedFile $file): array
    {
        $originalPath = $file->store('products', 'public');

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
