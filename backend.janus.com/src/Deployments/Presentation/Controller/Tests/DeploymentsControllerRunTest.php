<?php

/**
 * @file DeploymentsControllerRunTest.php
 *
 * Tests for DeploymentsController::run().
 *
 * @package App\Deployments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Presentation\Controller\DeploymentsController;
use App\Heimdall\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies the run action triggers a deployment and returns HTTP 201 or an error envelope.
 */
#[CoversClass(className: DeploymentsController::class)]
final class DeploymentsControllerRunTest extends DeploymentsControllerTest
{
    /**
     * Test that run() returns HTTP 201 with a data key on a successful HTTP trigger.
     */
    public function testRunReturns201OnSuccess(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());
        $this->httpResponse->method('getStatusCode')->willReturn(200);
        $this->httpResponse->method('getContent')->willReturn('ok');
        $this->httpClient->method('request')->willReturn($this->httpResponse);

        $response = $this->class->run('bbbbbbbb-0000-7000-8000-000000000001', $this->triggerDeploymentHandler);

        $this->assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertArrayHasKey('data', $body);
    }

    /**
     * Test that run() returns HTTP 404 when the provider is not found.
     */
    public function testRunReturns404WhenProviderNotFound(): void
    {
        $this->providerRepository->method('findById')->willReturn(null);

        $response = $this->class->run('nonexistent-id', $this->triggerDeploymentHandler);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('NOT_FOUND', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that run() returns HTTP 422 when the provider is inactive.
     */
    public function testRunReturns422WhenProviderInactive(): void
    {
        $inactiveProvider = DeploymentProvider::reconstitute(
            id:        'bbbbbbbb-0000-7000-8000-000000000001',
            name:      'Inactive',
            type:      DeploymentProviderType::WEBHOOK,
            url:       'https://example.com/hook',
            options:   null,
            isActive:  false,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );

        $this->providerRepository->method('findById')->willReturn($inactiveProvider);

        $response = $this->class->run('bbbbbbbb-0000-7000-8000-000000000001', $this->triggerDeploymentHandler);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $body = json_decode((string) $response->getContent(), true);
        $this->assertSame('PROVIDER_INACTIVE', $body['errors'][0]['extensions']['code']);
    }

    /**
     * Test that run() throws UnauthorizedException when the guard rejects the request.
     */
    public function testRunThrowsWhenUnauthenticated(): void
    {
        $controller = $this->buildControllerWithUnauthenticatedGuard();

        $this->expectException(UnauthorizedException::class);

        $controller->run('any-id', $this->triggerDeploymentHandler);
    }

    /**
     * Test that run() records the triggering user UUID in the deployment DTO.
     */
    public function testRunRecordsTriggeredByUser(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());
        $this->httpResponse->method('getStatusCode')->willReturn(200);
        $this->httpResponse->method('getContent')->willReturn('ok');
        $this->httpClient->method('request')->willReturn($this->httpResponse);

        $response = $this->class->run('bbbbbbbb-0000-7000-8000-000000000001', $this->triggerDeploymentHandler);
        $body     = json_decode((string) $response->getContent(), true);

        $this->assertSame(self::AUTH_USER_UUID, $body['data']['triggeredBy']);
    }
}
