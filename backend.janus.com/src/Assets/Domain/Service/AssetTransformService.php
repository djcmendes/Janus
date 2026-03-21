<?php

declare(strict_types=1);

namespace App\Assets\Domain\Service;

/**
 * Applies resize/crop/format transforms to an image using PHP GD.
 *
 * Fit modes:
 *   contain — scale to fit within bounds, preserving aspect ratio
 *   cover   — scale to fill bounds, preserving aspect ratio, then crop from center
 *   fill    — stretch to exact dimensions (no aspect-ratio preservation)
 */
final class AssetTransformService
{
    /**
     * Transform a local image file.
     *
     * @return array{content: string, mimeType: string}
     *
     * @throws \RuntimeException when GD cannot load or process the image
     */
    public function transform(
        string  $sourcePath,
        string  $sourceMime,
        ?int    $width,
        ?int    $height,
        string  $fit    = 'contain',
        string  $format = 'jpg',
    ): array {
        $src = $this->loadImage($sourcePath, $sourceMime);

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        [$dstW, $dstH, $cropX, $cropY, $srcCropW, $srcCropH] =
            $this->calculateDimensions($srcW, $srcH, $width, $height, $fit);

        $dst = imagecreatetruecolor($dstW, $dstH);

        // Preserve transparency for PNG and WebP targets
        if (in_array($format, ['png', 'webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $dstW - 1, $dstH - 1, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, $cropX, $cropY, $dstW, $dstH, $srcCropW, $srcCropH);

        imagedestroy($src);

        $content  = $this->renderToString($dst, $format);
        $mimeType = $this->formatToMime($format);

        imagedestroy($dst);

        return ['content' => $content, 'mimeType' => $mimeType];
    }

    // ------------------------------------------------------------------ private

    /** @return array{int,int,int,int,int,int} [dstW, dstH, srcX, srcY, srcCropW, srcCropH] */
    private function calculateDimensions(
        int     $srcW,
        int     $srcH,
        ?int    $targetW,
        ?int    $targetH,
        string  $fit,
    ): array {
        // No resize requested — serve original dimensions
        if ($targetW === null && $targetH === null) {
            return [$srcW, $srcH, 0, 0, $srcW, $srcH];
        }

        // Fill in missing dimension proportionally
        if ($targetW === null) {
            $targetW = (int) round($srcW * $targetH / $srcH);
        } elseif ($targetH === null) {
            $targetH = (int) round($srcH * $targetW / $srcW);
        }

        return match ($fit) {
            'fill'    => [$targetW, $targetH, 0, 0, $srcW, $srcH],
            'cover'   => $this->coverDimensions($srcW, $srcH, $targetW, $targetH),
            default   => $this->containDimensions($srcW, $srcH, $targetW, $targetH),
        };
    }

    /** Scale so image fills the target box, then crop center. */
    private function coverDimensions(int $srcW, int $srcH, int $targetW, int $targetH): array
    {
        $scaleW = $targetW / $srcW;
        $scaleH = $targetH / $srcH;
        $scale  = max($scaleW, $scaleH);

        $scaledW = (int) round($srcW * $scale);
        $scaledH = (int) round($srcH * $scale);

        // Crop region inside source (inverse of the scale)
        $cropW = (int) round($targetW / $scale);
        $cropH = (int) round($targetH / $scale);
        $cropX = (int) round(($srcW - $cropW) / 2);
        $cropY = (int) round(($srcH - $cropH) / 2);

        return [$targetW, $targetH, $cropX, $cropY, $cropW, $cropH];
    }

    /** Scale so the whole image fits within the target box. */
    private function containDimensions(int $srcW, int $srcH, int $targetW, int $targetH): array
    {
        $scaleW = $targetW / $srcW;
        $scaleH = $targetH / $srcH;
        $scale  = min($scaleW, $scaleH);

        $dstW = (int) round($srcW * $scale);
        $dstH = (int) round($srcH * $scale);

        return [$dstW, $dstH, 0, 0, $srcW, $srcH];
    }

    /** @return \GdImage */
    private function loadImage(string $path, string $mime): \GdImage
    {
        $image = match (true) {
            str_contains($mime, 'jpeg'), str_contains($mime, 'jpg') => imagecreatefromjpeg($path),
            str_contains($mime, 'png')                              => imagecreatefrompng($path),
            str_contains($mime, 'webp')                             => imagecreatefromwebp($path),
            str_contains($mime, 'gif')                              => imagecreatefromgif($path),
            default                                                  => false,
        };

        if ($image === false) {
            throw new \RuntimeException(sprintf('GD could not load image at "%s" (mime: %s).', $path, $mime));
        }

        return $image;
    }

    private function renderToString(\GdImage $image, string $format): string
    {
        ob_start();

        match ($format) {
            'png'   => imagepng($image),
            'webp'  => imagewebp($image),
            default => imagejpeg($image, null, 85),
        };

        $content = ob_get_clean();

        if ($content === false) {
            throw new \RuntimeException('GD failed to render image to string.');
        }

        return $content;
    }

    private function formatToMime(string $format): string
    {
        return match ($format) {
            'png'   => 'image/png',
            'webp'  => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
