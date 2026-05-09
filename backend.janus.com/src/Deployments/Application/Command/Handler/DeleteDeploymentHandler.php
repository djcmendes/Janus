<?php

/**
 * @file DeleteDeploymentHandler.php
 *
 * CQRS command handler — removes a deployment provider by UUID.
 *
 * @package App\Deployments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler;

use App\Deployments\Application\Command\DeleteDeploymentCommand;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

/**
 * Deletes a DeploymentProvider by UUID, throwing when none is found.
 */
final class DeleteDeploymentHandler
{
    /**
     * @param DeploymentProviderRepositoryInterface $repository Provider persistence gateway.
     */
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /**
     * Handles the command and removes the provider from persistence.
     *
     * @param  DeleteDeploymentCommand $command Carries the provider UUID.
     * @return void
     *
     * @throws DeploymentNotFoundException When no provider exists for the given UUID.
     */
    public function handle(DeleteDeploymentCommand $command): void
    {
        $provider = $this->repository->findById($command->id);

        if ($provider === null) {
            throw new DeploymentNotFoundException($command->id);
        }

        $this->repository->delete($provider);
    }
}
