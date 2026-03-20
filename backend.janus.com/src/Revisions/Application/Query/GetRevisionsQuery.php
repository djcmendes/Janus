<?php

declare(strict_types=1);

namespace App\Revisions\Application\Query;

final class GetRevisionsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $collection = null,
        public readonly ?string $item       = null,
    ) {}
}
