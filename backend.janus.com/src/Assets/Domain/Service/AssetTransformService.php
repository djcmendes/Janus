<?php

/**
 * @file AssetTransformService.php
 *
 * GD-based image transformation service supporting resize, crop, and format conversion.
 *
 * @package App\Assets\Domain\Service
 * @author  David Mendes
 */

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
     * Transforms a local image file and returns its rendered binary content and MIME type.
     *
     * @param  string      $sourcePath  Absolute filesystem path to the source image.
     * @param  string      $sourceMime  MIME type of the source image (e.g. 'image/jpeg').
     * @param  ?int        $width       Target width in pixels, or null to derive proportionally.
     * @param  ?int        $height      Target height in pixels, or null to derive proportionally.
     * @param  string      $fit         Resize mode: 'contain', 'cover', or 'fill' (default 'contain').
     * @param  string      $format      Output format: 'jpg', 'png', or 'webp' (default 'jpg').
     *
     * @return array{content: string, mimeType: string}  Rendered binary and resolved MIME type.
     *
     * @throws \RuntimeException  When GD cannot load the source file or render the output.
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

    /**
     * Computes destination and crop dimensions for the requested fit mode.
     *
     * When both $targetW and $targetH are null the source dimensions are returned unchanged.
     * A missing single dimension is derived proportionally before the fit is applied.
     *
     * @param  int    $srcW     Width of the loaded source image in pixels.
     * @param  int    $srcH     Height of the loaded source image in pixels.
     * @param  ?int   $targetW  Requested output width, or null.
     * @param  ?int   $targetH  Requested output height, or null.
     * @param  string $fit      One of 'contain', 'cover', or 'fill'.
     *
     * @return array{int,int,int,int,int,int}  [dstW, dstH, srcX, srcY, srcCropW, srcCropH]
     */
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

    /**
     * Scales the source to fill the target box completely, then computes center-crop offsets.
     *
     * @param  int $srcW     Source image width in pixels.
     * @param  int $srcH     Source image height in pixels.
     * @param  int $targetW  Desired output width in pixels.
     * @param  int $targetH  Desired output height in pixels.
     *
     * @return array{int,int,int,int,int,int}  [dstW, dstH, srcX, srcY, srcCropW, srcCropH]
     */
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

    /**
     * Scales the source so the entire image fits within the target box, preserving aspect ratio.
     *
     * @param  int $srcW     Source image width in pixels.
     * @param  int $srcH     Source image height in pixels.
     * @param  int $targetW  Maximum output width in pixels.
     * @param  int $targetH  Maximum output height in pixels.
     *
     * @return array{int,int,int,int,int,int}  [dstW, dstH, srcX, srcY, srcCropW, srcCropH]
     */
    private function containDimensions(int $srcW, int $srcH, int $targetW, int $targetH): array
    {
        $scaleW = $targetW / $srcW;
        $scaleH = $targetH / $srcH;
        $scale  = min($scaleW, $scaleH);

        $dstW = (int) round($srcW * $scale);
        $dstH = (int) round($srcH * $scale);

        return [$dstW, $dstH, 0, 0, $srcW, $srcH];
    }

    /**
     * Loads an image from disk into a GD resource using the appropriate decoder for the MIME type.
     *
     * @param  string $path  Absolute path to the image file.
     * @param  string $mime  MIME type used to select the GD loader function.
     *
     * @return \GdImage  The loaded GD image resource.
     *
     * @throws \RuntimeException  When the MIME type is unsupported or GD fails to decode the file.
     */
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

    /**
     * Renders a GD image resource to a binary string using the requested output format.
     *
     * @param  \GdImage $image   The GD image resource to render.
     * @param  string   $format  Output format: 'png', 'webp', or any other value for JPEG at quality 85.
     *
     * @return string  Raw binary image data.
     *
     * @throws \RuntimeException  When the output buffer fails to capture GD's output.
     */
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

    /**
     * Maps a GD format string to the corresponding MIME type.
     *
     * @param  string $format  One of 'png', 'webp', or any other value (treated as JPEG).
     *
     * @return string  The MIME type string (e.g. 'image/png').
     */
    private function formatToMime(string $format): string
    {
        return match ($format) {
            'png'   => 'image/png',
            'webp'  => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
