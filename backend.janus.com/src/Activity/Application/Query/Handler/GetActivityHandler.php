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
     * Returns a page of Activity DTOs and the total matching record count.
     *
     * @param GetActivityQuery $query Pagination and filter parameters.
     * @return array{data: ActivityDto[], total: int} Paged results alongside the unfiltered total.
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

        $total = $this->repository->countAll(
            collection: $query->collection,
            action:     $query->action,
            userId:     $query->userId,
        );

        return [
            'data'  => array_map(callback: ActivityDto::fromEntity(...), array: $items),
            'total' => $total,
        ];
    }
}
