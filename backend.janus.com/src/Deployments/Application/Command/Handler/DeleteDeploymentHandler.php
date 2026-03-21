<?php

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler;

use App\Deployments\Application\Command\DeleteDeploymentCommand;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

final class DeleteDeploymentHandler
{
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /** @throws DeploymentNotFoundException */
    public function handle(DeleteDeploymentCommand $command): void
    {
        $provider = $this->repository->findById($command->id);

        if ($provider === null) {
            throw new DeploymentNotFoundException($command->id);
        }

        $this->repository->delete($provider);
    }
}
