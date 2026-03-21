<?php

declare(strict_types=1);

namespace App\Collections\Application\Query\Handler;

use App\Collections\Application\DTO\CollectionDto;
use App\Collections\Application\Query\GetCollectionsQuery;
use App\Collections\Domain\Repository\CollectionMetaRepositoryInterface;

final class GetCollectionsHandler
{
    public function __construct(
        private readonly CollectionMetaRepositoryInterface $repository,
    ) {}

    /** @return array{data: CollectionDto[], total: int} */
    public function handle(GetCollectionsQuery $query): array
    {
        $collections = $this->repository->findPaginated($query->limit, $query->offset);
        $total       = $this->repository->count();

        return [
            'data'  => array_map(CollectionDto::fromEntity(...), $collections),
            'total' => $total,
        ];
    }
}
