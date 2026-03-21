<?php

declare(strict_types=1);

namespace App\Policies\Application\Query\Handler;

use App\Policies\Application\DTO\PolicyDto;
use App\Policies\Application\Query\GetPolicyByIdQuery;
use App\Policies\Domain\Exception\PolicyNotFoundException;
use App\Policies\Domain\Repository\PolicyRepositoryInterface;

final class GetPolicyByIdHandler
{
    public function __construct(
        private readonly PolicyRepositoryInterface $repository,
    ) {}

    public function handle(GetPolicyByIdQuery $query): PolicyDto
    {
        $policy = $this->repository->findById($query->id);

        if ($policy === null) {
            throw new PolicyNotFoundException($query->id);
        }

        return PolicyDto::fromEntity($policy);
    }
}
