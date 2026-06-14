<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductImageOptimizer
{
    public function store(UploadedFile $file): string
    {
        $source = @imagecreatefromstring($file->getContent());

        if ($source === false) {
            throw new RuntimeException('The uploaded product image could not be decoded.');
        }

        try {
            $source = $this->orientImage($source, $file);
            $optimized = $this->resize(
                $source,
                max(1, (int) config('storefront.product_images.max_width', 1600)),
                max(1, (int) config('storefront.product_images.max_height', 1600)),
            );

            try {
                ob_start();
                $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
                $quality = min(100, max(0, (int) config('storefront.product_images.quality', 82)));

                $encoded = $extension === 'webp'
                    ? imagewebp($optimized, null, $quality)
                    : imagejpeg($optimized, null, $quality);

                if (!$encoded) {
                    throw new RuntimeException('The product image could not be encoded.');
                }

                $contents = ob_get_clean();
            } catch (\Throwable $exception) {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }

                throw $exception;
            } finally {
                if ($optimized !== $source) {
                    imagedestroy($optimized);
                }
            }
        } finally {
            imagedestroy($source);
        }

        $path = 'products/'.Str::uuid().'.'.$extension;

        if (!Storage::disk('public')->put($path, $contents)) {
            throw new RuntimeException('The optimized product image could not be written.');
        }

        return $path;
    }

    private function resize(\GdImage $source, int $maxWidth, int $maxHeight): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min($maxWidth / $width, $maxHeight / $height, 1);

        if ($scale === 1.0) {
            return $source;
        }

        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $target;
    }

    private function orientImage(\GdImage $source, UploadedFile $file): \GdImage
    {
        if (!function_exists('exif_read_data') || $file->getMimeType() !== 'image/jpeg') {
            return $source;
        }

        $orientation = @exif_read_data($file->getPathname())['Orientation'] ?? 1;
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $source;
        }

        $rotated = imagerotate($source, $angle, 0);

        if ($rotated === false) {
            return $source;
        }

        imagedestroy($source);

        return $rotated;
    }
}
