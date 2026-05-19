<?php

/**
 * @file TriggerDeploymentHandlerHandleTest.php
 *
 * Tests for TriggerDeploymentHandler::handle().
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\TriggerDeploymentHandler;
use App\Deployments\Application\Command\TriggerDeploymentCommand;
use App\Deployments\Application\DTO\DeploymentDto;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use App\Deployments\Domain\Exception\DeploymentProviderInactiveException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies the full trigger lifecycle: provider lookup, HTTP call, status recording, and DTO return.
 */
#[CoversClass(className: TriggerDeploymentHandler::class)]
final class TriggerDeploymentHandlerHandleTest extends TriggerDeploymentHandlerTest
{
    /**
     * Test that handle() throws DeploymentNotFoundException when the provider is not found.
     */
    public function testHandleThrowsWhenProviderNotFound(): void
    {
        $this->providerRepository->method('findById')->willReturn(null);

        $this->expectException(DeploymentNotFoundException::class);

        $this->class->handle(new TriggerDeploymentCommand('nonexistent-id', null));
    }

    /**
     * Test that handle() throws DeploymentProviderInactiveException when the provider is inactive.
     */
    public function testHandleThrowsWhenProviderInactive(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider(false));

        $this->expectException(DeploymentProviderInactiveException::class);

        $this->class->handle(new TriggerDeploymentCommand('pppppppp-0000-7000-8000-000000000001', null));
    }

    /**
     * Test that handle() saves the deployment twice (RUNNING then final status).
     */
    public function testHandleSavesDeploymentTwice(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());
        $this->deploymentRepository->expects($this->exactly(2))->method('save');

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getContent')->willReturn('ok');
        $this->httpClient->method('request')->willReturn($this->response);

        $this->class->handle(new TriggerDeploymentCommand('pppppppp-0000-7000-8000-000000000001', null));
    }

    /**
     * Test that handle() returns a DeploymentDto on a successful HTTP response.
     */
    public function testHandleReturnsDtoOnSuccess(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getContent')->willReturn('ok');
        $this->httpClient->method('request')->willReturn($this->response);

        $dto = $this->class->handle(new TriggerDeploymentCommand('pppppppp-0000-7000-8000-000000000001', null));

        $this->assertInstanceOf(DeploymentDto::class, $dto);
        $this->assertSame('success', $dto->status);
    }

    /**
     * Test that handle() marks the run as FAILURE when the HTTP response is non-2xx.
     */
    public function testHandleMarksFailureOnNon2xxResponse(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());

        $this->response->method('getStatusCode')->willReturn(500);
        $this->response->method('getContent')->willReturn('error');
        $this->httpClient->method('request')->willReturn($this->response);

        $dto = $this->class->handle(new TriggerDeploymentCommand('pppppppp-0000-7000-8000-000000000001', null));

        $this->assertSame('failure', $dto->status);
    }

    /**
     * Test that handle() marks the run as FAILURE when the HTTP client throws an exception.
     */
    public function testHandleMarksFailureOnHttpClientException(): void
    {
        $this->providerRepository->method('findById')->willReturn($this->makeProvider());
        $this->httpClient->method('request')->willThrowException(new \RuntimeException('connection refused'));

        $dto = $this->class->handle(new TriggerDeploymentCommand('pppppppp-0000-7000-8000-000000000001', null));

        $this->assertSame('failure', $dto->status);
        $this->assertStringContainsString('connection refused', $dto->log ?? '');
    }
}
