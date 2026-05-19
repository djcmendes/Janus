<?php

/**
 * @file CreateDeploymentHandlerHandleTest.php
 *
 * Tests for CreateDeploymentHandler::handle().
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\CreateDeploymentCommand;
use App\Deployments\Application\Command\Handler\CreateDeploymentHandler;
use App\Deployments\Application\DTO\DeploymentProviderDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that handle() persists a provider and returns its DTO.
 */
#[CoversClass(className: CreateDeploymentHandler::class)]
final class CreateDeploymentHandlerHandleTest extends CreateDeploymentHandlerTest
{
    /**
     * Test that handle() calls save() on the repository.
     */
    public function testHandleCallsSave(): void
    {
        $this->repository->expects($this->once())->method('save');

        $this->class->handle(new CreateDeploymentCommand(
            name:     'Netlify Production',
            type:     'netlify',
            url:      'https://api.netlify.com/build_hooks/abc123',
            options:  null,
            isActive: true,
        ));
    }

    /**
     * Test that handle() returns a DeploymentProviderDto.
     */
    public function testHandleReturnsDeploymentProviderDto(): void
    {
        $dto = $this->class->handle(new CreateDeploymentCommand(
            name:     'Netlify Production',
            type:     'netlify',
            url:      'https://api.netlify.com/build_hooks/abc123',
            options:  null,
            isActive: true,
        ));

        $this->assertInstanceOf(DeploymentProviderDto::class, $dto);
    }

    /**
     * Test that handle() maps the provider name to the DTO.
     */
    public function testHandleMapsNameToDto(): void
    {
        $dto = $this->class->handle(new CreateDeploymentCommand(
            name:     'My Provider',
            type:     'webhook',
            url:      'https://example.com/hook',
        ));

        $this->assertSame('My Provider', $dto->name);
    }

    /**
     * Test that handle() maps the isActive flag to the DTO.
     */
    public function testHandleMapsIsActiveToDto(): void
    {
        $dto = $this->class->handle(new CreateDeploymentCommand(
            name:     'Inactive',
            type:     'custom',
            url:      'https://example.com/hook',
            isActive: false,
        ));

        $this->assertFalse($dto->isActive);
    }
}
