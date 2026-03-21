<?php

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler;

use App\Deployments\Application\Command\CreateDeploymentCommand;
use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

final class CreateDeploymentHandler
{
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    public function handle(CreateDeploymentCommand $command): DeploymentProviderDto
    {
        $type     = DeploymentProviderType::from($command->type);
        $provider = new DeploymentProvider($command->name, $type, $command->url);
        $provider->setOptions($command->options);
        $provider->setIsActive($command->isActive);

        $this->repository->save($provider);

        return DeploymentProviderDto::fromEntity($provider);
    }
}
