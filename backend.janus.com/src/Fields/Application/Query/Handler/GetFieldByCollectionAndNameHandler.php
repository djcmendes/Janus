<?php

/**
 * @file GetFieldByCollectionAndNameHandler.php
 *
 * CQRS query handler for retrieving a single field by collection and column name.
 *
 * @package App\Fields\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldByCollectionAndNameQuery;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles GetFieldByCollectionAndNameQuery — returns a single FieldDto or throws.
 */
final class GetFieldByCollectionAndNameHandler
{
    /**
     * @param FieldMetaRepositoryInterface $repository Field persistence store.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /**
     * Retrieves a single field identified by collection and column name.
     *
     * @param  GetFieldByCollectionAndNameQuery $query Collection and field name identifiers.
     * @return FieldDto                                The matching field as a read model.
     *
     * @throws FieldNotFoundException When no field with the given name exists in the collection.
     */
    public function handle(GetFieldByCollectionAndNameQuery $query): FieldDto
    {
        $field = $this->repository->findByCollectionAndField($query->collection, $query->field);

        if ($field === null) {
            throw new FieldNotFoundException($query->collection, $query->field);
        }

        return FieldDto::fromEntity($field);
    }
}
