<?php

declare(strict_types=1);

namespace App\Relations\Application\Query\Handler;

use App\Relations\Application\DTO\RelationDto;
use App\Relations\Application\Query\GetRelationsQuery;
use App\Relations\Domain\Repository\RelationRepositoryInterface;

final class GetRelationsHandler
{
    public function __construct(
        private readonly RelationRepositoryInterface $repository,
    ) {}

    /** @return array{data: RelationDto[], total: int} */
    public function handle(GetRelationsQuery $query): array
    {
        $relations = $this->repository->findPaginated($query->limit, $query->offset);
        $total     = $this->repository->count();

        return [
            'data'  => array_map(RelationDto::fromEntity(...), $relations),
            'total' => $total,
        ];
    }
}
