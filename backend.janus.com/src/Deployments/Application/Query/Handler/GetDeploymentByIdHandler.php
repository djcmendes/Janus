<?php

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentByIdQuery;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

final class GetDeploymentByIdHandler
{
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /** @throws DeploymentNotFoundException */
    public function handle(GetDeploymentByIdQuery $query): DeploymentProviderDto
    {
        $provider = $this->repository->findById($query->id);

        if ($provider === null) {
            throw new DeploymentNotFoundException($query->id);
        }

        return DeploymentProviderDto::fromEntity($provider);
    }
}
