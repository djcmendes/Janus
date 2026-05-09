<?php

/**
 * @file CreateFieldHandler.php
 *
 * CQRS command handler for creating a new field metadata record.
 * Validates the target collection exists, checks for name uniqueness,
 * then creates both the domain record and the DDL column (unless alias).
 *
 * @package App\Fields\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\CreateFieldCommand;
use App\Fields\Application\DTO\FieldDto;
use App\Fields\Domain\Entity\FieldMeta;
use App\Fields\Domain\Enum\FieldType;
use App\Fields\Domain\Exception\FieldAlreadyExistsException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles CreateFieldCommand — validates, persists the FieldMeta record, and adds the DDL column.
 */
final class CreateFieldHandler
{
    /**
     * @param FieldMetaRepositoryInterface      $fieldRepository      Field persistence store.
     * @param CollectionMetaRepositoryInterface $collectionRepository Collection presence check.
     * @param SchemaManagerService              $schemaManager        DDL column management.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface      $fieldRepository,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
        private readonly SchemaManagerService              $schemaManager,
    ) {}

    /**
     * Creates a new field record and adds the corresponding database column.
     *
     * ALIAS-typed fields skip DDL since they produce no database column.
     *
     * @param  CreateFieldCommand          $command Field creation payload.
     * @return FieldDto                             The created field as a read model.
     *
     * @throws CollectionNotFoundException  When the target collection does not exist.
     * @throws FieldAlreadyExistsException  When a field with the same name already exists.
     * @throws \InvalidArgumentException    When the field name fails format validation.
     * @throws \ValueError                  When the type string is not a valid FieldType value.
     */
    public function handle(CreateFieldCommand $command): FieldDto
    {
        if ($this->collectionRepository->findByName($command->collection) === null) {
            throw new CollectionNotFoundException($command->collection);
        }

        if ($this->fieldRepository->findByCollectionAndField($command->collection, $command->field) !== null) {
            throw new FieldAlreadyExistsException($command->collection, $command->field);
        }

        $type = FieldType::from($command->type);

        $fieldMeta = new FieldMeta($command->collection, $command->field, $type);
        $fieldMeta->setLabel($command->label);
        $fieldMeta->setNote($command->note);
        $fieldMeta->setRequired($command->required);
        $fieldMeta->setReadonly($command->readonly);
        $fieldMeta->setHidden($command->hidden);
        $fieldMeta->setSortOrder($command->sortOrder);
        $fieldMeta->setInterface($command->interface);
        $fieldMeta->setOptions($command->options);

        if (!$type->isAlias()) {
            $this->schemaManager->addColumn($command->collection, $command->field, $type->toColumnDdl());
        }

        $this->fieldRepository->save($fieldMeta);

        return FieldDto::fromEntity($fieldMeta);
    }
}
