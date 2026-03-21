<?php

declare(strict_types=1);

namespace App\Panels\Application\Query;

final class GetPanelsQuery
{
    public function __construct(
        public readonly int     $limit,
        public readonly int     $offset,
        public readonly ?string $dashboardId = null,
    ) {}
}
