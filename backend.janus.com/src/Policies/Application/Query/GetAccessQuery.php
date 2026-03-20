<?php

declare(strict_types=1);

namespace App\Policies\Application\Query;

final class GetAccessQuery
{
    public function __construct(
        public readonly int $limit  = 25,
        public readonly int $offset = 0,
    ) {}
}
