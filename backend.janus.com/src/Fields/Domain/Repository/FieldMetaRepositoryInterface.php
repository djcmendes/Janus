<?php

/**
 * @file FieldMetaRepositoryInterface.php
 *
 * Repository contract for FieldMeta domain entity persistence operations.
 * The application layer depends only on this interface.
 *
 * @package App\Fields\Domain\Repository
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Domain\Repository;

use App\Fields\Domain\Entity\FieldMeta;

/**
 * Defines persistence operations for FieldMeta domain entities.
 *
 * Concrete implementations live in the Infrastructure layer. The application
 * layer depends only on this interface, keeping it decoupled from the ORM.
 */
interface FieldMetaRepositoryInterface
{
    /**
     * Persists a FieldMeta record.
     *
     * @param FieldMeta $field The field to persist.
     * @param bool      $flush Whether to flush the unit of work immediately (default: true).
     */
    public function save(FieldMeta $field, bool $flush = true): void;

    /**
     * Removes a FieldMeta record from the store.
     *
     * @param FieldMeta $field The field to remove.
     */
    public function delete(FieldMeta $field): void;

    /**
     * Finds a single field by its collection and column name.
     *
     * @param  string        $collection Collection name.
     * @param  string        $field      Column name.
     * @return FieldMeta|null The matching field, or null when not found.
     */
    public function findByCollectionAndField(string $collection, string $field): ?FieldMeta;

    /**
     * Returns all fields belonging to a collection, ordered by sortOrder then createdAt.
     *
     * @param  string      $collection Collection name.
     * @return FieldMeta[]             All fields for the collection.
     */
    public function findByCollection(string $collection): array;

    /**
     * Returns a paginated slice of all field records, ordered by collection and sortOrder.
     *
     * @param  int         $limit  Maximum number of records to return.
     * @param  int         $offset Zero-based offset for pagination.
     * @return FieldMeta[]         Paginated field records.
     */
    public function findPaginated(int $limit, int $offset): array;

    /**
     * Returns the total number of field records across all collections.
     *
     * @return int Total count.
     */
    public function countAll(): int;

    /**
     * Deletes all field records belonging to the given collection.
     *
     * @param string $collection Collection name whose fields should be removed.
     */
    public function deleteByCollection(string $collection): void;
}
