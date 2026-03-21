<?php

declare(strict_types=1);

namespace App\Flows\Application\Query;

final class GetOperationsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $flowId = null,
    ) {}
}
