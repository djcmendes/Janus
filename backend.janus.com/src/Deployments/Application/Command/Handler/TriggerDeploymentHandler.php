<?php

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

final class TriggerDeploymentHandler
{
    public function __construct(
        private readonly DeploymentProviderRepositoryInterface $providerRepository,
        private readonly DeploymentRepositoryInterface         $deploymentRepository,
        private readonly HttpClientInterface                   $httpClient,
    ) {}

    /**
     * Triggers the provider's build hook and persists a Deployment run record.
     *
     * @throws DeploymentNotFoundException
     * @throws DeploymentProviderInactiveException
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

        $deployment = new Deployment((string) $provider->getId(), $command->triggeredBy);
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
