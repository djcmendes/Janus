<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Query;

final class GetDashboardByIdQuery
{
    public function __construct(public readonly string $id) {}
}
