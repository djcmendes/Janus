<?php

declare(strict_types=1);

namespace App\Users\Application\Query;

final class GetUsersQuery
{
    public function __construct(
        public readonly int $limit  = 25,
        public readonly int $offset = 0,
    ) {}
}
