<?php

declare(strict_types=1);

namespace App\Items\Application\Query\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Items\Application\Query\GetItemByIdQuery;
use App\Items\Domain\Exception\ItemNotFoundException;
use App\Items\Domain\Service\ItemsService;

final class GetItemByIdHandler
{
    public function __construct(
        private readonly ItemsService                       $itemsService,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
    ) {}

    /**
     * @throws CollectionNotFoundException
     * @throws ItemNotFoundException
     */
    public function handle(GetItemByIdQuery $query): array
    {
        if ($this->collectionRepository->findByName($query->collection) === null) {
            throw new CollectionNotFoundException($query->collection);
        }

        return $this->itemsService->findById($query->collection, $query->id);
    }
}
