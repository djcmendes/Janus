<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardsQuery;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

final class GetDashboardsHandler
{
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    /** @return array{data: DashboardDto[], total: int} */
    public function handle(GetDashboardsQuery $query): array
    {
        $dashboards = $this->repository->findAll($query->limit, $query->offset, $query->userId);
        $total      = $this->repository->countAll($query->userId);

        return [
            'data'  => array_map(DashboardDto::fromEntity(...), $dashboards),
            'total' => $total,
        ];
    }
}
