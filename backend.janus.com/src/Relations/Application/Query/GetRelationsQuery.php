<?php

declare(strict_types=1);

namespace App\Relations\Application\Query;

final class GetRelationsQuery
{
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
