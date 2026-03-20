<?php

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionsQuery;
use App\Versions\Domain\Repository\VersionRepositoryInterface;

final class GetVersionsHandler
{
    public function __construct(private readonly VersionRepositoryInterface $repository) {}

    /** @return array{data: VersionDto[], total: int} */
    public function handle(GetVersionsQuery $query): array
    {
        return [
            'data'  => array_map(
                VersionDto::fromEntity(...),
                $this->repository->findPaginated($query->limit, $query->offset, $query->collection, $query->item),
            ),
            'total' => $this->repository->countAll($query->collection, $query->item),
        ];
    }
}
