<?php

declare(strict_types=1);

namespace App\Versions\Application\Query;

final class GetVersionsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection,
        public readonly ?string $item,
    ) {}
}
