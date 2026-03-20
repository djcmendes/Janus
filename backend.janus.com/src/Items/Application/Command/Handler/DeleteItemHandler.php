<?php

declare(strict_types=1);

namespace App\Items\Application\Command\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Items\Application\Command\DeleteItemCommand;
use App\Items\Domain\Exception\ItemNotFoundException;
use App\Items\Domain\Service\ItemsService;

final class DeleteItemHandler
{
    public function __construct(
        private readonly ItemsService                       $itemsService,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
    ) {}

    /**
     * @throws CollectionNotFoundException
     * @throws ItemNotFoundException
     */
    public function handle(DeleteItemCommand $command): void
    {
        if ($this->collectionRepository->findByName($command->collection) === null) {
            throw new CollectionNotFoundException($command->collection);
        }

        $this->itemsService->delete($command->collection, $command->id);
    }
}
