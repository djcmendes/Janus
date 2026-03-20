<?php

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentsQuery;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

final class GetDeploymentsHandler
{
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /** @return array{data: DeploymentProviderDto[], total: int} */
    public function handle(GetDeploymentsQuery $query): array
    {
        return [
            'data'  => array_map(
                DeploymentProviderDto::fromEntity(...),
                $this->repository->findPaginated($query->limit, $query->offset),
            ),
            'total' => $this->repository->countAll(),
        ];
    }
}
