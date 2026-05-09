<?php

/**
 * @file GetDashboardsHandler.php
 *
 * Handles paginated dashboard list queries.
 *
 * @package App\Dashboards\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardsQuery;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;

/**
 * Returns a paginated set of DashboardDtos with a total count for meta.
 */
final class GetDashboardsHandler
{
    /**
     * @param DashboardRepositoryInterface $repository Persistence layer for dashboards.
     */
    public function __construct(private readonly DashboardRepositoryInterface $repository) {}

    /**
     * Executes the list-dashboards query.
     *
     * @param  GetDashboardsQuery                     $query Pagination and filter parameters.
     * @return array{data: DashboardDto[], total: int}       DTOs and total count.
     */
    public function handle(GetDashboardsQuery $query): array
    {
        $dashboards = $this->repository->findPaginated($query->limit, $query->offset, $query->userId);
        $total      = $this->repository->countAll($query->userId);

        return [
            'data'  => array_map(DashboardDto::fromEntity(...), $dashboards),
            'total' => $total,
        ];
    }
}
