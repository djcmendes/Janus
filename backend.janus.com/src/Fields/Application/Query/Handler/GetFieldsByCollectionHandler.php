<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsByCollectionQuery;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class GetFieldsByCollectionHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /** @return FieldDto[] */
    public function handle(GetFieldsByCollectionQuery $query): array
    {
        $fields = $this->repository->findByCollection($query->collection);
        return array_map(FieldDto::fromEntity(...), $fields);
    }
}
