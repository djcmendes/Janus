<?php

/**
 * @file GetAssetQuery.php
 *
 * Query payload for retrieving and optionally transforming a stored asset.
 *
 * @package App\Assets\Application\Query
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Assets\Application\Query;

/**
 * Carries the asset identifier and optional transform parameters passed
 * from the controller to GetAssetHandler.
 */
final readonly class GetAssetQuery
{
    /**
     * @param string $id      UUID of the file record to retrieve.
     * @param ?int   $width   Target width in pixels, or null to derive proportionally.
     * @param ?int   $height  Target height in pixels, or null to derive proportionally.
     * @param string $fit     Resize strategy: 'contain', 'cover', or 'fill'.
     * @param string $format  Output image format: 'jpg', 'png', or 'webp'.
     */
    public function __construct(
        public string $id,
        public ?int   $width,
        public ?int   $height,
        public string $fit,
        public string $format,
    ) {}
}
