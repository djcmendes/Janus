<?php

/**
 * @file TriggerDeploymentHandlerTest.php
 *
 * Abstract base providing setUp / tearDown and factory helpers for TriggerDeploymentHandler test cases.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\TriggerDeploymentHandler;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Repository\DeploymentProviderRepositoryInterface;
use App\Deployments\Domain\Repository\DeploymentRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Common setup, teardown, and factory helpers for TriggerDeploymentHandler test suites.
 */
#[CoversClass(TriggerDeploymentHandler::class)]
abstract class TriggerDeploymentHandlerTest extends TestCase
{
    /** @var MockObject&DeploymentProviderRepositoryInterface */
    protected MockObject $providerRepository;

    /** @var MockObject&DeploymentRepositoryInterface */
    protected MockObject $deploymentRepository;

    /** @var MockObject&HttpClientInterface */
    protected MockObject $httpClient;

    /** @var MockObject&ResponseInterface */
    protected MockObject $response;

    /** @var TriggerDeploymentHandler */
    protected TriggerDeploymentHandler $class;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->providerRepository   = $this->createMock(DeploymentProviderRepositoryInterface::class);
        $this->deploymentRepository = $this->createMock(DeploymentRepositoryInterface::class);
        $this->httpClient           = $this->createMock(HttpClientInterface::class);
        $this->response             = $this->createMock(ResponseInterface::class);

        $this->class = new TriggerDeploymentHandler(
            providerRepository:   $this->providerRepository,
            deploymentRepository: $this->deploymentRepository,
            httpClient:           $this->httpClient,
        );
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        unset(
            $this->providerRepository,
            $this->deploymentRepository,
            $this->httpClient,
            $this->response,
            $this->class,
        );
    }

    /**
     * Creates an active DeploymentProvider stub.
     *
     * @param bool $isActive Whether the provider is active.
     *
     * @return DeploymentProvider
     */
    protected function makeProvider(bool $isActive = true): DeploymentProvider
    {
        return DeploymentProvider::reconstitute(
            id:        'pppppppp-0000-7000-8000-000000000001',
            name:      'Netlify Production',
            type:      DeploymentProviderType::NETLIFY,
            url:       'https://api.netlify.com/build_hooks/abc123',
            options:   null,
            isActive:  $isActive,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
    }
}
