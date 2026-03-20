<?php

declare(strict_types=1);

namespace App\Policies\Application\Query\Handler;

use App\Policies\Application\DTO\AccessDto;
use App\Policies\Application\Query\GetAccessQuery;
use App\Policies\Domain\Repository\AccessRepositoryInterface;

final class GetAccessHandler
{
    public function __construct(
        private readonly AccessRepositoryInterface $repository,
    ) {}

    /** @return array{data: AccessDto[], total: int} */
    public function handle(GetAccessQuery $query): array
    {
        return [
            'data'  => array_map(AccessDto::fromEntity(...), $this->repository->findAll($query->limit, $query->offset)),
            'total' => $this->repository->count(),
        ];
    }
}
