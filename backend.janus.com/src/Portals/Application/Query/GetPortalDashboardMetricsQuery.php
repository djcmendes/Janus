<?php
declare(strict_types=1);
namespace App\Portals\Application\Query;

final class GetPortalDashboardMetricsQuery
{
    public function __construct(
        public readonly string $portalId,
        public readonly int    $activityLimit = 10,
    ) {}
}
