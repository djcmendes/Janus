<?php

/**
 * @file GetFieldsByCollectionHandler.php
 *
 * CQRS query handler for retrieving all fields belonging to a specific collection.
 *
 * @package App\Fields\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsByCollectionQuery;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles GetFieldsByCollectionQuery — returns all FieldDto records for a collection.
 */
final class GetFieldsByCollectionHandler
{
    /**
     * @param FieldMetaRepositoryInterface $repository Field persistence store.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /**
     * Retrieves all fields belonging to the specified collection.
     *
     * @param  GetFieldsByCollectionQuery $query Collection filter.
     * @return FieldDto[]                        All fields for the collection.
     */
    public function handle(GetFieldsByCollectionQuery $query): array
    {
        $fields = $this->repository->findByCollection($query->collection);
        return array_map(FieldDto::fromEntity(...), $fields);
    }
}
