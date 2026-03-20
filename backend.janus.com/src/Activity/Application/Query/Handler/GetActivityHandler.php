<?php

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityQuery;
use App\Activity\Domain\Repository\ActivityRepositoryInterface;

final class GetActivityHandler
{
    public function __construct(
        private readonly ActivityRepositoryInterface $repository,
    ) {}

    /** @return array{data: ActivityDto[], total: int} */
    public function handle(GetActivityQuery $query): array
    {
        $items = $this->repository->findAll(
            $query->limit,
            $query->offset,
            $query->collection,
            $query->action,
            $query->userId,
        );

        $total = $this->repository->countAll(
            $query->collection,
            $query->action,
            $query->userId,
        );

        return [
            'data'  => array_map(ActivityDto::fromEntity(...), $items),
            'total' => $total,
        ];
    }
}
