<?php

/**
 * @file UpdateCollectionHandler.php
 *
 * Application handler for UpdateCollectionCommand.
 *
 * @package App\Collections\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\UpdateCollectionCommand;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

/**
 * Handles partial updates to a collection's metadata.
 *
 * Only fields explicitly included in the command payload are applied;
 * fields set to UpdateCollectionCommand::UNCHANGED are left as-is.
 */
final class UpdateCollectionHandler
{
    /**
     * Constructor
     *
     * @param CollectionMetaRepositoryInterface $repository Persists and retrieves CollectionMeta records.
     */
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /**
     * Executes the update-collection use case.
     *
     * Retrieves the collection by name, applies only the provided field values,
     * and persists the updated entity.
     *
     * @param  UpdateCollectionCommand    $command Partial update payload with field-level sentinels.
     * @return CollectionDto                       DTO of the updated collection.
     *
     * @throws CollectionNotFoundException When no collection with the given name exists.
     */
    public function handle(UpdateCollectionCommand $command): CollectionDto
    {
        $collection = $this->repository->findByName($command->name);

        if ($collection === null) {
            throw new CollectionNotFoundException($command->name);
        }

        if ($command->label !== null) {
            $collection->setLabel($command->label);
        }
        if ($command->icon !== UpdateCollectionCommand::UNCHANGED) {
            $collection->setIcon($command->icon);
        }
        if ($command->note !== UpdateCollectionCommand::UNCHANGED) {
            $collection->setNote($command->note);
        }
        if ($command->hidden !== null) {
            $collection->setHidden($command->hidden);
        }
        if ($command->singleton !== null) {
            $collection->setSingleton($command->singleton);
        }
        if ($command->sortField !== UpdateCollectionCommand::UNCHANGED) {
            $collection->setSortField($command->sortField);
        }

        $this->repository->save($collection);

        return CollectionDto::fromEntity($collection);
    }
}
