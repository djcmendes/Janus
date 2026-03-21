<?php

declare(strict_types=1);

namespace App\Items\Application\Query\Handler;

use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;
use App\Items\Application\Query\GetItemsQuery;
use App\Items\Domain\Service\ItemsService;

final class GetItemsHandler
{
    public function __construct(
        private readonly ItemsService                       $itemsService,
        private readonly CollectionMetaRepositoryInterface $collectionRepository,
    ) {}

    /**
     * @return array{data: array[], total: int}
     * @throws CollectionNotFoundException
     */
    public function handle(GetItemsQuery $query): array
    {
        if ($this->collectionRepository->findByName($query->collection) === null) {
            throw new CollectionNotFoundException($query->collection);
        }

        return $this->itemsService->findPaginated($query->collection, $query->limit, $query->offset);
    }
}
