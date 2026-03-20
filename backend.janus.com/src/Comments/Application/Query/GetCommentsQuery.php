<?php

declare(strict_types=1);

namespace App\Comments\Application\Query;

final class GetCommentsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $item       = null,
    ) {}
}
