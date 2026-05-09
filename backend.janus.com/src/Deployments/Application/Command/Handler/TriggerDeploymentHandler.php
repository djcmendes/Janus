<?php

/**
 * @file TriggerDeploymentHandler.php
 *
 * CQRS command handler — triggers a deployment run against a configured provider.
 *
 * @package App\Deployments\Application\Command\Handler
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler;

use App\Deployments\Application\Command\TriggerDeploymentCommand;
use App\Deployments\Application\DTO\DeploymentDto;
use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Exception\DeploymentProviderInactiveException;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use App\Deployments\Domain\Repository\DeploymentRepositoryInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fires the provider's build hook via HTTP, persists the run record, and returns its DTO.
 *
 * Workflow:
 *  1. Load provider — throw DeploymentNotFoundException when missing.
 *  2. Guard active flag — throw DeploymentProviderInactiveException when inactive.
 *  3. Persist RUNNING run record.
 *  4. POST to provider URL (15-second timeout).
 *  5. Mark run SUCCESS (2xx) or FAILURE (non-2xx / exception).
 *  6. Persist final state and return the DTO.
 */
final class TriggerDeploymentHandler
{
    /**
     * @param DeploymentProviderRepositoryInterface $providerRepository   Provider persistence gateway.
     * @param DeploymentRepositoryInterface         $deploymentRepository Run record persistence gateway.
     * @param HttpClientInterface                   $httpClient           HTTP client for build-hook calls.
     */
    public function __construct(
        private readonly DeploymentProviderRepositoryInterface $providerRepository,
        private readonly DeploymentRepositoryInterface         $deploymentRepository,
        private readonly HttpClientInterface                   $httpClient,
    ) {}

    /**
     * Triggers the provider's build hook and persists a Deployment run record.
     *
     * @param  TriggerDeploymentCommand $command Carries provider UUID and triggering user.
     * @return DeploymentDto                     The completed run record as a serialisable DTO.
     *
     * @throws DeploymentNotFoundException         When no provider exists for the given UUID.
     * @throws DeploymentProviderInactiveException When the provider's active flag is false.
     */
    public function handle(TriggerDeploymentCommand $command): DeploymentDto
    {
        $provider = $this->providerRepository->findById($command->providerId);

        if ($provider === null) {
            throw new DeploymentNotFoundException($command->providerId);
        }

        if (!$provider->isActive()) {
            throw new DeploymentProviderInactiveException($command->providerId);
        }

        $deployment = new Deployment($provider->getId(), $command->triggeredBy);
        $deployment->setStatus(DeploymentRunStatus::RUNNING);
        $this->deploymentRepository->save($deployment);

        try {
            $options = $provider->getOptions() ?? [];

            $response = $this->httpClient->request('POST', $provider->getUrl(), [
                'headers' => $options['headers'] ?? [],
                'timeout' => 15,
            ]);

            $statusCode = $response->getStatusCode();
            $body       = $response->getContent(false);

            $runStatus = ($statusCode >= 200 && $statusCode < 300)
                ? DeploymentRunStatus::SUCCESS
                : DeploymentRunStatus::FAILURE;

            $log = sprintf('[HTTP %d] %s', $statusCode, mb_substr($body, 0, 2000));
        } catch (\Throwable $e) {
            $runStatus = DeploymentRunStatus::FAILURE;
            $log       = '[ERROR] ' . $e->getMessage();
        }

        $deployment->complete($runStatus, $log);
        $this->deploymentRepository->save($deployment);

        return DeploymentDto::fromEntity($deployment);
    }
}
