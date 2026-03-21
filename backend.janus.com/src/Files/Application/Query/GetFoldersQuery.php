<?php

declare(strict_types=1);

namespace App\Files\Application\Query;

final class GetFoldersQuery
{
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
