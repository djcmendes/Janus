<?php

/**
 * @file GetExtensionByIdHandler.php
 *
 * CQRS query handler — retrieves a single Extension by UUID.
 *
 * @package App\Extensions\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\Query\Handler;

use App\Extensions\Application\DTO\ExtensionDto;
use App\Extensions\Application\Query\GetExtensionByIdQuery;
use App\Extensions\Domain\Exception\ExtensionNotFoundException;
use App\Extensions\Domain\Repository\ExtensionRepositoryInterface;

/**
 * Fetches a single Extension by UUID and returns it as a DTO.
 */
final class GetExtensionByIdHandler
{
    /**
     * @param ExtensionRepositoryInterface $repository Extension persistence gateway.
     */
    public function __construct(private readonly ExtensionRepositoryInterface $repository) {}

    /**
     * Returns the extension DTO for the requested UUID.
     *
     * @param  GetExtensionByIdQuery    $query Carries the UUID to look up.
     * @return ExtensionDto                     The found extension as a serialisable read model.
     *
     * @throws ExtensionNotFoundException When no extension exists for the given UUID.
     */
    public function handle(GetExtensionByIdQuery $query): ExtensionDto
    {
        $extension = $this->repository->findById($query->id);

        if ($extension === null) {
            throw new ExtensionNotFoundException($query->id);
        }

        return ExtensionDto::fromEntity($extension);
    }
}
