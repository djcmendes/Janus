<?php

/**
 * @file GetDashboardByIdHandler.php
 *
 * Handles single-dashboard retrieval queries.
 *
 * @package App\Dashboards\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardByIdQuery;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

/**
 * Retrieves a single Dashboard by UUID and returns a DashboardDto.
 */
final class GetDashboardByIdHandler
{
    /**
     * @param DashboardRepositoryInterface $repository Persistence layer for dashboards.
     */
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    /**
     * Executes the get-dashboard-by-id query.
     *
     * @param  GetDashboardByIdQuery   $query UUID of the dashboard to retrieve.
     * @return DashboardDto                   DTO reflecting the found record.
     *
     * @throws DashboardNotFoundException When no dashboard exists with the given UUID.
     */
    public function handle(GetDashboardByIdQuery $query): DashboardDto
    {
        $dashboard = $this->repository->findById($query->id);

        if ($dashboard === null) {
            throw new DashboardNotFoundException($query->id);
        }

        return DashboardDto::fromEntity($dashboard);
    }
}
