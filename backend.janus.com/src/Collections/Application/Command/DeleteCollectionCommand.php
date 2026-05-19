<?php

/**
 * @file DeleteCollectionCommand.php
 *
 * CQRS command payload for deleting a collection and all its associated data.
 *
 * @package App\Collections\Application\Command
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command;

/**
 * Carries the collection name identifying which collection to delete.
 *
 * Deletion cascades: the handler removes all FieldMeta records for the collection,
 * drops the database table via SchemaManagerService, and removes the CollectionMeta record.
 */
final class DeleteCollectionCommand
{
    /**
     * Constructor
     *
     * @param string $name The collection name to delete.
     */
    public function __construct(
        public readonly string $name
    ) {}
}
