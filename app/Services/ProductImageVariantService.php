<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

class ProductImageVariantService
{
    private const VARIANTS = [
        'swatch' => ['width' => 80, 'height' => 80, 'fit' => 'cover', 'quality' => 66],
        'thumb' => ['width' => 240, 'height' => 240, 'fit' => 'cover', 'quality' => 76],
        'card' => ['width' => 600, 'height' => 800, 'fit' => 'cover', 'quality' => 78],
        'detail' => ['width' => 1200, 'height' => null, 'fit' => 'contain', 'quality' => 82],
        'zoom' => ['width' => 1600, 'height' => null, 'fit' => 'contain', 'quality' => 84],
    ];

    public function diskName(): string
    {
        return config('filesystems.disks.r2.bucket') ? 'r2' : 'public';
    }

    public function url(?string $path, string $variant): ?string
    {
        if (! $path) {
            return null;
        }

        if ($this->isRemote($path)) {
            return $path;
        }

        return Storage::disk($this->diskName())->url($this->variantPath($path, $variant));
    }

    public function path(?string $path, string $variant): ?string
    {
        if (! $path || $this->isRemote($path)) {
            return null;
        }

        return $this->variantPath($path, $variant);
    }

    public function srcset(?string $path, array $variants): ?string
    {
        if (! $path || $this->isRemote($path)) {
            return null;
        }

        $srcset = collect($variants)
            ->map(fn (int $width, string $variant) => $this->url($path, $variant).' '.$width.'w')
            ->implode(', ');

        return $srcset !== '' ? $srcset : null;
    }

    public function generate(string $sourcePath, bool $force = false): int
    {
        try {
            if ($this->isRemote($sourcePath)) {
                return 0;
            }

            $disk = Storage::disk($this->diskName());

            if (! $disk->exists($sourcePath)) {
                return 0;
            }

            $source = imagecreatefromstring($disk->get($sourcePath));

            if (! $source) {
                return 0;
            }

            $written = 0;

            try {
                foreach (self::VARIANTS as $variant => $preset) {
                    $targetPath = $this->variantPath($sourcePath, $variant);

                    if (! $force && $disk->exists($targetPath)) {
                        continue;
                    }

                    $image = $this->resize($source, $preset);
                    ob_start();
                    imagewebp($image, null, $preset['quality']);
                    $contents = (string) ob_get_clean();
                    imagedestroy($image);

                    $disk->put($targetPath, $contents, [
                        'visibility' => 'public',
                        'CacheControl' => 'public, max-age=31536000, immutable',
                        'ContentType' => 'image/webp',
                    ]);
                    $written++;
                }
            } finally {
                imagedestroy($source);
            }

            return $written;
        } catch (Throwable $exception) {
            report($exception);

            return 0;
        }
    }

    private function variantPath(string $sourcePath, string $variant): string
    {
        if (! array_key_exists($variant, self::VARIANTS)) {
            throw new InvalidArgumentException("Unsupported product image variant [{$variant}].");
        }

        $sourcePath = trim($sourcePath, '/');
        $directory = trim(dirname($sourcePath), '.\\/');
        $slug = basename($directory);
        $base = pathinfo($sourcePath, PATHINFO_FILENAME);

        return "products/{$slug}/{$variant}/{$base}.webp";
    }

    private function resize(\GdImage $source, array $preset): \GdImage
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = (int) $preset['width'];
        $targetHeight = $preset['height'] ? (int) $preset['height'] : (int) round($sourceHeight * ($targetWidth / $sourceWidth));
        $scale = $preset['fit'] === 'cover'
            ? max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight)
            : min($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);
        $resizeWidth = (int) ceil($sourceWidth * $scale);
        $resizeHeight = (int) ceil($sourceHeight * $scale);
        $offsetX = (int) floor(($targetWidth - $resizeWidth) / 2);
        $offsetY = (int) floor(($targetHeight - $resizeHeight) / 2);

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = imagecolorallocate($target, 245, 240, 234);
        imagefill($target, 0, 0, $background);
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

        return $target;
    }

    private function isRemote(string $path): bool
    {
        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://');
    }
}
