<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OptimizesImages
{
    public static function store(UploadedFile $file, string $directory, int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 82): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());

        if (! extension_loaded('gd') || $extension === 'gif') {
            return $file->store($directory, 'public');
        }

        $source = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'png' => @imagecreatefrompng($file->getRealPath()),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };

        if (! $source) {
            return $file->store($directory, 'public');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';
        $temp = tempnam(sys_get_temp_dir(), 'optimized-image-');

        if (! imagewebp($target, $temp, $quality)) {
            imagedestroy($source);
            imagedestroy($target);
            @unlink($temp);
            return $file->store($directory, 'public');
        }

        Storage::disk('public')->put($path, file_get_contents($temp));

        imagedestroy($source);
        imagedestroy($target);
        @unlink($temp);

        return $path;
    }
}
