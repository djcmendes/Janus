<?php

declare(strict_types=1);

namespace App\Policies\Application\Query\Handler;

use App\Policies\Application\DTO\PolicyDto;
use App\Policies\Application\Query\GetPoliciesQuery;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;

final class GetPoliciesHandler
{
    public function __construct(
        private readonly PolicyRepositoryInterface $repository,
    ) {}

    /** @return array{data: PolicyDto[], total: int} */
    public function handle(GetPoliciesQuery $query): array
    {
        return [
            'data'  => array_map(PolicyDto::fromEntity(...), $this->repository->findAll($query->limit, $query->offset)),
            'total' => $this->repository->count(),
        ];
    }
}
