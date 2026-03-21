<?php

declare(strict_types=1);

namespace App\Items\Application\Command\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Items\Application\Command\CreateItemCommand;
use App\Items\Domain\Service\ItemsService;

final class CreateItemHandler
{
    public function __construct(
        private readonly ItemsService                       $itemsService,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
    ) {}

    /**
     * @throws CollectionNotFoundException
     */
    public function handle(CreateItemCommand $command): array
    {
        if ($this->collectionRepository->findByName($command->collection) === null) {
            throw new CollectionNotFoundException($command->collection);
        }

        return $this->itemsService->create($command->collection, $command->data);
    }
}
