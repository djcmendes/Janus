<?php

/**
 * @file CreateCollectionHandler.php
 *
 * Application handler for CreateCollectionCommand.
 *
 * @package App\Collections\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\CreateCollectionCommand;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Entity\CollectionMeta;
use App\Collections\Domain\Exception\CollectionAlreadyExistsException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles collection creation: validates uniqueness, creates the CollectionMeta record,
 * provisions the database table, and registers the primary-key FieldMeta.
 */
final class CreateCollectionHandler
{
    /**
     * Constructor
     *
     * @param CollectionMetaRepositoryInterface $repository     Persists and retrieves CollectionMeta records.
     * @param FieldMetaRepositoryInterface      $fieldRepository Persists FieldMeta records for the new PK field.
     * @param SchemaManagerService              $schemaManager   Executes DDL to create the backing table.
     */
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly SchemaManagerService              $schemaManager,
    ) {}

    /**
     * Executes the create-collection use case.
     *
     * Validates that no collection with the same name exists, persists the CollectionMeta,
     * creates the database table, and saves the primary-key FieldMeta.
     *
     * @param  CreateCollectionCommand        $command Validated creation payload.
     * @return CollectionDto                           DTO of the newly created collection.
     *
     * @throws CollectionAlreadyExistsException When a collection with the given name already exists.
     */
    public function handle(CreateCollectionCommand $command): CollectionDto
    {
        if ($this->repository->findByName($command->name) !== null) {
            throw new CollectionAlreadyExistsException($command->name);
        }

        $collection = new CollectionMeta($command->name);
        $collection->setLabel($command->label);
        $collection->setIcon($command->icon);
        $collection->setNote($command->note);
        $collection->setHidden($command->hidden);
        $collection->setSingleton($command->singleton);
        $collection->setSortField($command->sortField);

        $this->schemaManager->createTable($command->name, $command->primaryKeyField, $command->primaryKeyType);

        $pkType    = FieldType::from($command->primaryKeyType);
        $pkField   = new FieldMeta($command->name, $command->primaryKeyField, $pkType);
        $pkField->setHidden(true);
        $pkField->setReadonly(true);

        $this->fieldRepository->save($pkField, false);
        $this->repository->save($collection);

        return CollectionDto::fromEntity($collection);
    }
}
