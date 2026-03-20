<?php

declare(strict_types=1);

namespace App\Roles\Application\Query;

final class GetRolesQuery
{
    public function __construct(
        public readonly int $limit  = 25,
        public readonly int $offset = 0,
    ) {}
}
