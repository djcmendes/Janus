<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldByCollectionAndNameQuery;
use App\Fields\Domain\Exception\FieldNotFoundException;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class GetFieldByCollectionAndNameHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /** @throws FieldNotFoundException */
    public function handle(GetFieldByCollectionAndNameQuery $query): FieldDto
    {
        $field = $this->repository->findByCollectionAndField($query->collection, $query->field);

        if ($field === null) {
            throw new FieldNotFoundException($query->collection, $query->field);
        }

        return FieldDto::fromEntity($field);
    }
}
