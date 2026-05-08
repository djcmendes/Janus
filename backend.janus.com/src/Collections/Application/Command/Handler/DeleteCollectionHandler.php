<?php

/**
 * @file DeleteCollectionHandler.php
 *
 * Application handler for DeleteCollectionCommand.
 *
 * @package App\Collections\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\DeleteCollectionCommand;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles collection deletion: removes all field metadata, deletes the CollectionMeta
 * record, and drops the backing database table.
 */
final class DeleteCollectionHandler
{
    /**
     * Constructor
     *
     * @param CollectionMetaRepositoryInterface $repository     Persists and retrieves CollectionMeta records.
     * @param SchemaManagerService              $schemaManager   Executes DDL to drop the backing table.
     * @param FieldMetaRepositoryInterface      $fieldRepository Removes FieldMeta records before the table drop.
     */
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
        private readonly SchemaManagerService              $schemaManager,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
    ) {}

    /**
     * Executes the delete-collection use case.
     *
     * Retrieves the collection by name, removes all associated field metadata,
     * deletes the CollectionMeta record, and drops the backing database table.
     *
     * @param  DeleteCollectionCommand    $command Identifies the collection to delete.
     * @return void
     *
     * @throws CollectionNotFoundException When no collection with the given name exists.
     */
    public function handle(DeleteCollectionCommand $command): void
    {
        $collection = $this->repository->findByName($command->name);

        if ($collection === null) {
            throw new CollectionNotFoundException($command->name);
        }

        $this->fieldRepository->deleteByCollection($command->name);
        $this->repository->delete($collection);
        $this->schemaManager->dropTable($command->name);
    }
}
