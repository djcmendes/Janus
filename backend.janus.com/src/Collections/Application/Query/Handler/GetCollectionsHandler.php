<?php

/**
 * @file GetCollectionsHandler.php
 *
 * Application handler for GetCollectionsQuery.
 *
 * @package App\Collections\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionsQuery;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

/**
 * Handles paginated listing of all collections.
 *
 * Fetches a page of CollectionMeta domain entities and maps them to DTOs,
 * along with the total record count for pagination metadata.
 */
final class GetCollectionsHandler
{
    /**
     * Constructor
     *
     * @param CollectionMetaRepositoryInterface $repository Provides paginated access to CollectionMeta records.
     */
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /**
     * Executes the list-collections use case.
     *
     * @param  GetCollectionsQuery                     $query Pagination parameters.
     * @return array{data: CollectionDto[], total: int}        Page of DTOs and the total record count.
     */
    public function handle(GetCollectionsQuery $query): array
    {
        $collections = $this->repository->findPaginated($query->limit, $query->offset);
        $total       = $this->repository->count();

        return [
            'data'  => array_map(CollectionDto::fromEntity(...), $collections),
            'total' => $total,
        ];
    }
}
