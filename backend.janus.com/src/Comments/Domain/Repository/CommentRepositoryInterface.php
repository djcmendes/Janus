<?php

/**
 * @file CommentRepositoryInterface.php
 *
 * Domain contract for Comment persistence operations.
 *
 * @package App\Comments\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Repository;

use App\Comments\Domain\Entity\Comment;

/**
 * Defines the persistence contract for Comment domain entities.
 */
interface CommentRepositoryInterface
{
    /**
     * Persists a Comment record to the database.
     *
     * @param Comment $comment The domain entity to store.
     * @param bool    $flush   Whether to flush the entity manager immediately.
     *
     * @return void
     */
    public function save(Comment $comment, bool $flush = true): void;

    /**
     * Removes a Comment record from the database.
     *
     * @param Comment $comment The domain entity whose persisted record should be deleted.
     *
     * @return void
     */
    public function delete(Comment $comment): void;

    /**
     * Finds a single Comment domain entity by its UUID primary key.
     *
     * @param string $id The UUID of the comment record to retrieve.
     *
     * @return Comment|null The matching domain entity, or null if no record exists.
     */
    public function findById(string $id): ?Comment;

    /**
     * Returns a page of Comment domain entities with optional filters.
     *
     * @param int         $limit      Maximum number of records to return.
     * @param int         $offset     Number of records to skip (pagination offset).
     * @param string|null $collection Filter to a specific collection, or null for all.
     * @param string|null $item       Filter to a specific item, or null for all.
     *
     * @return Comment[] Ordered array of domain Comment entities.
     */
    public function findPaginated(
        int     $limit,
        int     $offset,
        ?string $collection = null,
        ?string $item       = null,
    ): array;

    /**
     * Returns the total count of Comment records matching the given filters.
     *
     * @param string|null $collection Filter to a specific collection, or null for all.
     * @param string|null $item       Filter to a specific item, or null for all.
     *
     * @return int Total number of matching records.
     */
    public function countAll(
        ?string $collection = null,
        ?string $item       = null,
    ): int;
}
