<?php

/**
 * @file GetDeploymentByIdHandlerHandleTest.php
 *
 * Tests for GetDeploymentByIdHandler::handle().
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentByIdQuery;
use App\Deployments\Application\Query\Handler\GetDeploymentByIdHandler;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that handle() returns a DTO or throws DeploymentNotFoundException.
 */
#[CoversClass(GetDeploymentByIdHandler::class)]
final class GetDeploymentByIdHandlerHandleTest extends GetDeploymentByIdHandlerTest
{
    /**
     * Creates a DeploymentProvider for use as repository stub return value.
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
     * Test that handle() returns a DeploymentProviderDto when the provider is found.
     */
    public function testHandleReturnsDtoWhenProviderFound(): void
    {
        $this->repository->method('findById')->willReturn($this->makeProvider());

        $dto = $this->class->handle(new GetDeploymentByIdQuery('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertInstanceOf(DeploymentProviderDto::class, $dto);
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $dto->id);
    }

    /**
     * Test that handle() throws DeploymentNotFoundException when the provider is not found.
     */
    public function testHandleThrowsWhenProviderNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(DeploymentNotFoundException::class);

        $this->class->handle(new GetDeploymentByIdQuery('nonexistent-id'));
    }
}
