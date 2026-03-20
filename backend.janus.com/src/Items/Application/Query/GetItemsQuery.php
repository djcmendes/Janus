<?php

declare(strict_types=1);

namespace App\Items\Application\Query;

final class GetItemsQuery
{
    public function __construct(
        public readonly string $collection,
        public readonly int    $limit,
        public readonly int    $offset,
    ) {}
}
