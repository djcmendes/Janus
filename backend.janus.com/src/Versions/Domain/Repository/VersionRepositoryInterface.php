<?php

/**
 * @file VersionRepositoryInterface.php
 *
 * Domain repository contract for storing and retrieving Version records.
 *
 * @package App\Versions\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Repository;

use App\Versions\Domain\Entity\Version;

/**
 * Defines the persistence contract for Version records within the domain layer.
 *
 * Implementations live in the Infrastructure layer and must not be referenced
 * directly from domain or application code — only this interface should be used.
 */
interface VersionRepositoryInterface
{
    /**
     * Persists a Version record, inserting or updating as appropriate.
     *
     * @param Version $version The domain entity to store.
     * @param bool    $flush   Whether to flush immediately (default: true).
     */
    public function save(Version $version, bool $flush = true): void;

    /**
     * Permanently removes a Version record from the store.
     *
     * @param Version $version The domain entity to remove.
     */
    public function delete(Version $version): void;

    /**
     * Finds a single Version by its UUID primary key.
     *
     * @param  string       $id UUID of the version to retrieve.
     * @return Version|null     The matching entity, or null if not found.
     */
    public function findById(string $id): ?Version;

    /**
     * Finds a Version by the unique collection + item + key triplet.
     *
     * @param  string       $collection Collection name to match.
     * @param  string       $item       Item identifier to match.
     * @param  string       $key        Version label to match.
     * @return Version|null             The matching entity, or null if not found.
     */
    public function findByCollectionItemAndKey(string $collection, string $item, string $key): ?Version;

    /**
     * Returns a page of Version entities matching optional filters, ordered by createdAt descending.
     *
     * @param  int         $limit      Maximum number of records to return.
     * @param  int         $offset     Number of records to skip.
     * @param  string|null $collection Filter to a specific collection, or null for all.
     * @param  string|null $item       Filter to a specific item, or null for all.
     * @return Version[]               Ordered array of matching domain entities.
     */
    public function findPaginated(int $limit, int $offset, ?string $collection = null, ?string $item = null): array;

    /**
     * Returns the total count of Version records matching the given filters.
     *
     * @param  string|null $collection Filter to a specific collection, or null for all.
     * @param  string|null $item       Filter to a specific item, or null for all.
     * @return int                     Total number of matching records.
     */
    public function countAll(?string $collection = null, ?string $item = null): int;
}
