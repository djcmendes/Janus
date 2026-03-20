<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Query;

final class GetDashboardsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $userId = null,
    ) {}
}
