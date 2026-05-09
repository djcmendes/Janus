<?php

/**
 * @file CreateDeploymentHandler.php
 *
 * CQRS command handler — creates and persists a new deployment provider.
 *
 * @package App\Deployments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler;

use App\Deployments\Application\Command\CreateDeploymentCommand;
use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;

/**
 * Creates a new DeploymentProvider from the command payload and returns it as a DTO.
 */
final class CreateDeploymentHandler
{
    /**
     * @param DeploymentProviderRepositoryInterface $repository Provider persistence gateway.
     */
    public function __construct(private readonly DeploymentProviderRepositoryInterface $repository) {}

    /**
     * Handles the command, persists the new provider, and returns its DTO.
     *
     * @param  CreateDeploymentCommand $command Carries name, type, url, options, and isActive.
     * @return DeploymentProviderDto            The persisted provider as a serialisable DTO.
     */
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
