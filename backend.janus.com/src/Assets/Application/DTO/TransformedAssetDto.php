<?php

declare(strict_types=1);

namespace App\Assets\Application\DTO;

final class TransformedAssetDto
{
    public function __construct(
        public readonly string $content,
        public readonly string $mimeType,
        public readonly string $filename,
    ) {}
}
