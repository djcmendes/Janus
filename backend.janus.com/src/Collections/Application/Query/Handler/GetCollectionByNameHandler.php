<?php

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionByNameQuery;
use App\Collections\Domain\Exception\CollectionNotFoundException;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

final class GetCollectionByNameHandler
{
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /** @throws CollectionNotFoundException */
    public function handle(GetCollectionByNameQuery $query): CollectionDto
    {
        $collection = $this->repository->findByName($query->name);

        if ($collection === null) {
            throw new CollectionNotFoundException($query->name);
        }

        return CollectionDto::fromEntity($collection);
    }
}
