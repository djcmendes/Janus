<?php

declare(strict_types=1);

namespace App\Presets\Application\Query;

final class GetPresetsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $userId     = null,
    ) {}
}
