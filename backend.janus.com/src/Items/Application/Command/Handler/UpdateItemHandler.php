<?php

declare(strict_types=1);

namespace App\Items\Application\Command\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Items\Application\Command\UpdateItemCommand;
use App\Items\Domain\Exception\ItemNotFoundException;
use App\Items\Domain\Service\ItemsService;

final class UpdateItemHandler
{
    public function __construct(
        private readonly ItemsService                       $itemsService,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
    ) {}

    /**
     * @throws CollectionNotFoundException
     * @throws ItemNotFoundException
     */
    public function handle(UpdateItemCommand $command): array
    {
        if ($this->collectionRepository->findByName($command->collection) === null) {
            throw new CollectionNotFoundException($command->collection);
        }

        return $this->itemsService->update($command->collection, $command->id, $command->data);
    }
}
