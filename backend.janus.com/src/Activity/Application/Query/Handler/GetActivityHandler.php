<?php

/**
 * @file GetActivityHandler.php
 *
 * Query handler that retrieves a paginated list of Activity records.
 *
 * @package App\Activity\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityQuery;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;

/**
 * Handles GetActivityQuery, running the paginated query and the count query
 * in sequence and returning both results as a single array.
 */
final readonly class GetActivityHandler
{
    /**
     * Constructor
     *
     * @param ActivityRepositoryInterface $repository Repository used to query Activity records.
     */
    public function __construct(
        private ActivityRepositoryInterface $repository,
    ) {}

    /**
     * Returns a page of Activity DTOs alongside filtered and unfiltered totals.
     *
     * @param GetActivityQuery $query Pagination and filter parameters.
     * @return array{data: ActivityDto[], filter_total: int, unfiltered_total: int}
     *         Paged results, total matching the applied filters, and total unfiltered.
     */
    public function handle(GetActivityQuery $query): array
    {
        $items = $this->repository->findPaginated(
            limit:      $query->limit,
            offset:     $query->offset,
            collection: $query->collection,
            action:     $query->action,
            userId:     $query->userId,
        );

        $filterTotal = $this->repository->countAll(
            collection: $query->collection,
            action:     $query->action,
            userId:     $query->userId,
        );

        $unfilteredTotal = $this->repository->countAll();

        return [
            'data'             => array_map(callback: ActivityDto::fromEntity(...), array: $items),
            'filter_total'     => $filterTotal,
            'unfiltered_total' => $unfilteredTotal,
        ];
    }
}
