<?php

declare(strict_types=1);

namespace App\Fields\Application\Query;

final class GetFieldsQuery
{
    public function __construct(
        public readonly int $limit,
        public readonly int $offset,
    ) {}
}
