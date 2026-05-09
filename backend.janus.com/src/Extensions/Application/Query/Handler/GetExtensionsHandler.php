<?php

/**
 * @file GetExtensionsHandler.php
 *
 * CQRS query handler — retrieves a paginated list of extensions.
 *
 * @package App\Extensions\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionsQuery;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

/**
 * Fetches a paginated slice of extensions and returns them as DTOs alongside the total count.
 */
final class GetExtensionsHandler
{
    /**
     * @param ExtensionRepositoryInterface $repository Extension persistence gateway.
     */
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /**
     * Returns a paginated result set of extensions.
     *
     * @param  GetExtensionsQuery $query Carries limit, offset, type filter, and enabled filter.
     * @return array{data: ExtensionDto[], total: int} Mapped DTOs and total matching count.
     */
    public function handle(GetExtensionsQuery $query): array
    {
        $extensions = $this->repository->findPaginated($query->limit, $query->offset, $query->type, $query->enabled);
        $total      = $this->repository->countAll($query->type, $query->enabled);

        return [
            'data'  => array_map(ExtensionDto::fromEntity(...), $extensions),
            'total' => $total,
        ];
    }
}
