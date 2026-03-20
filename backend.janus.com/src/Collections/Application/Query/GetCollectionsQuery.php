<?php

declare(strict_types=1);

namespace App\Collections\Application\Query;

final class GetCollectionsQuery
{
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
