<?php

/**
 * @file ActivityRepositoryInterface.php
 *
 * Domain repository contract for Activity persistence and retrieval.
 *
 * @package App\Activity\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Repository;

use App\Activity\Domain\Entity\Activity;

/**
 * Defines the persistence operations available for Activity audit-log records.
 */
interface ActivityRepositoryInterface
{
    /**
     * Persists a new Activity record immediately.
     *
     * @param Activity $activity The activity entity to store.
     *
     * @return void
     */
    public function record(Activity $activity): void;

    /**
     * Finds a single Activity record by its UUID.
     *
     * @param string $id The UUID of the activity record to retrieve.
     *
     * @return Activity|null The matching entity, or null if no record exists.
     */
    public function findById(string $id): ?Activity;

    /**
     * Returns a page of Activity records with optional filters applied.
     *
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to a specific collection, or null for all.
     * @param string|null $action     Filter to a specific action type, or null for all.
     * @param string|null $userId     Filter to a specific user UUID, or null for all.
     *
     * @return Activity[] Ordered array of matching Activity entities.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): array;

    /**
     * Returns the total number of Activity records matching the given filters.
     *
     * @param string|null $collection Filter to a specific collection, or null for all.
     * @param string|null $action     Filter to a specific action type, or null for all.
     * @param string|null $userId     Filter to a specific user UUID, or null for all.
     *
     * @return int Total count of matching records.
     */
    public function countAll(
        ?string $collection = null,
        ?string $action     = null,
        ?string $userId     = null,
    ): int;
}
