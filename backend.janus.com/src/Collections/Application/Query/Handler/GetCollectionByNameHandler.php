<?php

/**
 * @file GetCollectionByNameHandler.php
 *
 * Application handler for GetCollectionByNameQuery.
 *
 * @package App\Collections\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionByNameQuery;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

/**
 * Handles fetching a single collection by its name.
 *
 * Returns a CollectionDto when the collection exists, or throws CollectionNotFoundException
 * when no record with the given name is found.
 */
final class GetCollectionByNameHandler
{
    /**
     * Constructor
     *
     * @param CollectionMetaRepositoryInterface $repository Provides lookup access to CollectionMeta records.
     */
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /**
     * Executes the get-collection-by-name use case.
     *
     * @param  GetCollectionByNameQuery   $query Identifies the collection to retrieve.
     * @return CollectionDto                     DTO of the found collection.
     *
     * @throws CollectionNotFoundException When no collection with the given name exists.
     */
    public function handle(GetCollectionByNameQuery $query): CollectionDto
    {
        $collection = $this->repository->findByName($query->name);

        if ($collection === null) {
            throw new CollectionNotFoundException($query->name);
        }

        return CollectionDto::fromEntity($collection);
    }
}
