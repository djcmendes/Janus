<?php

declare(strict_types=1);

namespace App\Activity\Application\Query;

final class GetActivityQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $action     = null,
        public readonly ?string $userId     = null,
    ) {}
}
