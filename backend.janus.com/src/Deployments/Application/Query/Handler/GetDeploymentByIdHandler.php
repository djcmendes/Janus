<?php

/**
 * @file GetDeploymentByIdHandler.php
 *
 * CQRS query handler — retrieves a single deployment provider by UUID.
 *
 * @package App\Deployments\Application\Query\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentByIdQuery;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

/**
 * Fetches a single DeploymentProvider by UUID and returns it as a DTO.
 */
final class GetDeploymentByIdHandler
{
    /**
     * @param DeploymentProviderRepositoryInterface $repository Provider persistence gateway.
     */
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /**
     * Handles the query and returns the provider DTO.
     *
     * @param  GetDeploymentByIdQuery $query Carries the provider UUID.
     * @return DeploymentProviderDto          The matching provider.
     *
     * @throws DeploymentNotFoundException When no provider exists for the given UUID.
     */
    public function handle(GetDeploymentByIdQuery $query): DeploymentProviderDto
    {
        $provider = $this->repository->findById($query->id);

        if ($provider === null) {
            throw new DeploymentNotFoundException($query->id);
        }

        return DeploymentProviderDto::fromEntity($provider);
    }
}
