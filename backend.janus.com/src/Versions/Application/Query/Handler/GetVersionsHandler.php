<?php

/**
 * @file GetVersionsHandler.php
 *
 * Query handler that returns a paginated list of Version records as DTOs.
 *
 * @package App\Versions\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionsQuery;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

/**
 * Handles GetVersionsQuery by delegating pagination and filtering to the repository
 * and mapping each result to a VersionDto.
 */
final class GetVersionsHandler
{
    /**
     * @param VersionRepositoryInterface $repository Storage and retrieval of Version records.
     */
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /**
     * Returns a paginated page of VersionDto objects and the total matching count.
     *
     * @param  GetVersionsQuery $query Pagination and filter parameters.
     * @return array{data: VersionDto[], total: int} Data page and total record count.
     */
    public function handle(GetVersionsQuery $query): array
    {
        return [
            'data'  => array_map(
                VersionDto::fromEntity(...),
                $this->repository->findPaginated($query->limit, $query->offset, $query->collection, $query->item),
            ),
            'total' => $this->repository->countAll($query->collection, $query->item),
        ];
    }
}
