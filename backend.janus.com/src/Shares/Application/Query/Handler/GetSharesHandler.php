<?php

declare(strict_types=1);

namespace App\Shares\Application\Query\Handler;

use App\Shares\Application\DTO\ShareDto;
use App\Shares\Application\Query\GetSharesQuery;
use App\Shares\Domain\Repository\ShareRepositoryInterface;

final class GetSharesHandler
{
    public function __construct(private readonly ShareRepositoryInterface $repository) {}

    /** @return array{data: ShareDto[], total: int} */
    public function handle(GetSharesQuery $query): array
    {
        $shares = $this->repository->findPaginated($query->limit, $query->offset, $query->collection, $query->userId);
        $total  = $this->repository->countAll($query->collection, $query->userId);

        return [
            'data'  => array_map(ShareDto::fromEntity(...), $shares),
            'total' => $total,
        ];
    }
}
