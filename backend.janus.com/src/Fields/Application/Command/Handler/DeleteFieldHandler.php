<?php

/**
 * @file DeleteFieldHandler.php
 *
 * CQRS command handler for removing a field metadata record and its database column.
 * ALIAS-typed fields skip the DDL drop since they have no physical column.
 *
 * @package App\Fields\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\Command\Handler;

use App\Collections\Infrastructure\Service\SchemaManagerService;
use App\Fields\Application\Command\DeleteFieldCommand;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

/**
 * Handles DeleteFieldCommand — removes the FieldMeta record and drops the DDL column.
 */
final class DeleteFieldHandler
{
    /**
     * @param FieldMetaRepositoryInterface $repository    Field persistence store.
     * @param SchemaManagerService         $schemaManager DDL column management.
     */
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
        private readonly SchemaManagerService         $schemaManager,
    ) {}

    /**
     * Removes the field record and drops its database column.
     *
     * ALIAS-typed fields do not have a physical column, so the DDL drop is skipped.
     *
     * @param  DeleteFieldCommand $command Field identification payload.
     *
     * @throws FieldNotFoundException When no field with the given name exists in the collection.
     */
    public function handle(DeleteFieldCommand $command): void
    {
        $field = $this->repository->findByCollectionAndField($command->collection, $command->field);

        if ($field === null) {
            throw new FieldNotFoundException($command->collection, $command->field);
        }

        $this->repository->delete($field);

        if (!$field->getType()->isAlias()) {
            $this->schemaManager->dropColumn($command->collection, $command->field);
        }
    }
}
