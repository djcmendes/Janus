<?php

/**
 * @file CollectionMetaRepositoryInterface.php
 *
 * Repository contract for collection metadata persistence.
 *
 * @package App\Collections\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Repository;

use App\Collections\Domain\Entity\CollectionMeta;

/**
 * Defines the persistence contract for CollectionMeta domain entities.
 *
 * The Infrastructure layer provides the Doctrine implementation; the Application
 * layer depends only on this interface, keeping it decoupled from the ORM.
 */
interface CollectionMetaRepositoryInterface
{
    /**
     * Persists a CollectionMeta record, inserting or updating as needed.
     *
     * @param CollectionMeta $collection The domain entity to store.
     * @param bool           $flush      Whether to flush immediately (default: true).
     *
     * @return void
     */
    public function save(CollectionMeta $collection, bool $flush = true): void;

    /**
     * Removes a CollectionMeta record from the database.
     *
     * @param CollectionMeta $collection The domain entity to remove.
     *
     * @return void
     */
    public function delete(CollectionMeta $collection): void;

    /**
     * Finds a single CollectionMeta record by its collection name.
     *
     * @param string $name The collection name to look up.
     *
     * @return CollectionMeta|null The matching domain entity, or null if not found.
     */
    public function findByName(string $name): ?CollectionMeta;

    /**
     * Returns a page of CollectionMeta domain entities.
     *
     * @param int $limit  Maximum number of records to return.
     * @param int $offset Number of records to skip (pagination offset).
     *
     * @return CollectionMeta[] Ordered array of domain entities.
     */
    public function findPaginated(int $limit, int $offset): array;

    /**
     * Returns the total count of CollectionMeta records.
     *
     * @param array<string, mixed> $criteria Optional filter criteria.
     *
     * @return int Total number of matching records.
     */
    public function count(array $criteria = []): int;
}
