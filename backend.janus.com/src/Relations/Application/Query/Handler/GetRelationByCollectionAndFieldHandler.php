<?php

declare(strict_types=1);

namespace App\Relations\Application\Query\Handler;

use App\Relations\Application\DTO\RelationDto;
use App\Relations\Application\Query\GetRelationByCollectionAndFieldQuery;
use App\Relations\Domain\Exception\RelationNotFoundException;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

final class GetRelationByCollectionAndFieldHandler
{
    public function __construct(
        private readonly RelationRepositoryInterface $repository,
    ) {}

    /** @throws RelationNotFoundException */
    public function handle(GetRelationByCollectionAndFieldQuery $query): RelationDto
    {
        $relation = $this->repository->findByCollectionAndField($query->collection, $query->field);

        if ($relation === null) {
            throw new RelationNotFoundException($query->collection, $query->field);
        }

        return RelationDto::fromEntity($relation);
    }
}
