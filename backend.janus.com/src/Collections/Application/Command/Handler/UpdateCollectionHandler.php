<?php

declare(strict_types=1);

namespace App\Collections\Application\Command\Handler;

use App\Collections\Application\Command\UpdateCollectionCommand;
use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

final class UpdateCollectionHandler
{
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /** @throws CollectionNotFoundException */
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
