<?php

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardByIdQuery;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

final class GetDashboardByIdHandler
{
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    public function handle(GetDashboardByIdQuery $query): DashboardDto
    {
        $dashboard = $this->repository->findById($query->id);

        if ($dashboard === null) {
            throw new DashboardNotFoundException($query->id);
        }

        return DashboardDto::fromEntity($dashboard);
    }
}
