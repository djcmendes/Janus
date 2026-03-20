<?php

declare(strict_types=1);

namespace App\Assets\Application\Query;

final class GetAssetQuery
{
    public function __construct(
        public readonly string  $id,
        public readonly ?int    $width,
        public readonly ?int    $height,
        public readonly string  $fit,
        public readonly string  $format,
    ) {}
}
