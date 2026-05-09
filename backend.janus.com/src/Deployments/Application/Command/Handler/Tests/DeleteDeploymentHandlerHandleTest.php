<?php

/**
 * @file DeleteDeploymentHandlerHandleTest.php
 *
 * Tests for DeleteDeploymentHandler::handle().
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\DeleteDeploymentCommand;
use App\Deployments\Application\Command\Handler\DeleteDeploymentHandler;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that handle() deletes a provider or throws DeploymentNotFoundException.
 */
#[CoversClass(DeleteDeploymentHandler::class)]
final class DeleteDeploymentHandlerHandleTest extends DeleteDeploymentHandlerTest
{
    /**
     * Creates a DeploymentProvider stub for repository mock.
     *
     * @return DeploymentProvider
     */
    private function makeProvider(): DeploymentProvider
    {
        return DeploymentProvider::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Netlify Production',
            type:      DeploymentProviderType::NETLIFY,
            url:       'https://api.netlify.com/build_hooks/abc123',
            options:   null,
            isActive:  true,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
    }

    /**
     * Test that handle() calls delete() on the repository when provider is found.
     */
    public function testHandleCallsDeleteWhenProviderFound(): void
    {
        $this->repository->method('findById')->willReturn($this->makeProvider());
        $this->repository->expects($this->once())->method('delete');

        $this->class->handle(new DeleteDeploymentCommand('aaaaaaaa-0000-7000-8000-000000000001'));
    }

    /**
     * Test that handle() throws DeploymentNotFoundException when provider is not found.
     */
    public function testHandleThrowsWhenProviderNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(DeploymentNotFoundException::class);

        $this->class->handle(new DeleteDeploymentCommand('nonexistent-id'));
    }
}
