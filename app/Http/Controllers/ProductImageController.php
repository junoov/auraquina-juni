<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductImageController extends Controller
{
    public function card(string $path): BinaryFileResponse|RedirectResponse
    {
        abort_unless($this->isSafeImagePath($path), 404);

        $diskName = config('filesystems.disks.r2.bucket') ? 'r2' : 'public';
        abort_unless(Storage::disk($diskName)->exists($path), 404);

        $cachePath = storage_path('app/public/image-cache/product-cards/'.sha1($path).'.webp');
        File::ensureDirectoryExists(dirname($cachePath), 0755);

        if (! file_exists($cachePath)) {
            $this->writeCardThumbnail($diskName, $path, $cachePath);
        }

        return response()
            ->file($cachePath, [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Content-Type' => 'image/webp',
            ]);
    }

    private function isSafeImagePath(string $path): bool
    {
        return ! str_contains($path, '..')
            && ! str_starts_with($path, '/')
            && ! str_contains($path, '://')
            && preg_match('/\.(jpe?g|png|webp)$/i', $path) === 1;
    }

    private function writeCardThumbnail(string $diskName, string $path, string $cachePath): void
    {
        $contents = Storage::disk($diskName)->get($path);
        $source = imagecreatefromstring($contents);

        if (! $source) {
            abort(404);
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = 480;
        $targetHeight = 640;
        $scale = max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $resizeWidth = (int) ceil($sourceWidth * $scale);
        $resizeHeight = (int) ceil($sourceHeight * $scale);
        $offsetX = (int) floor(($targetWidth - $resizeWidth) / 2);
        $offsetY = 0;

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled(
            $target,
            $source,
            $offsetX,
            $offsetY,
            0,
            0,
            $resizeWidth,
            $resizeHeight,
            $sourceWidth,
            $sourceHeight,
        );

        imagewebp($target, $cachePath, 76);
        imagedestroy($source);
        imagedestroy($target);
    }
}
