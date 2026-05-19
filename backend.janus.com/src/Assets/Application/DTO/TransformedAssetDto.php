<?php

/**
 * @file TransformedAssetDto.php
 *
 * Data transfer object carrying the result of an asset transformation.
 *
 * @package App\Assets\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\DTO;

/**
 * Carries the binary content, MIME type, and original filename produced by
 * AssetTransformService after processing a requested asset.
 */
final class TransformedAssetDto
{
    /**
     * @param string $content   Raw binary output of the GD transformation.
     * @param string $mimeType  MIME type of the rendered image (e.g. 'image/jpeg').
     * @param string $filename  Original download filename taken from the File domain entity.
     */
    public function __construct(
        public readonly string $content,
        public readonly string $mimeType,
        public readonly string $filename,
    ) {}
}
