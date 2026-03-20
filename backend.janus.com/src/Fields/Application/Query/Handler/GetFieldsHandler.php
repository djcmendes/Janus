<?php

declare(strict_types=1);

namespace App\Fields\Application\Query\Handler;

use App\Fields\Application\DTO\FieldDto;
use App\Fields\Application\Query\GetFieldsQuery;
use App\Fields\Domain\Repository\FieldMetaRepositoryInterface;

final class GetFieldsHandler
{
    public function __construct(
        private readonly FieldMetaRepositoryInterface $repository,
    ) {}

    /** @return array{data: FieldDto[], total: int} */
    public function handle(GetFieldsQuery $query): array
    {
        $fields = $this->repository->findAll($query->limit, $query->offset);
        $total  = $this->repository->countAll();

        return [
            'data'  => array_map(FieldDto::fromEntity(...), $fields),
            'total' => $total,
        ];
    }
}
