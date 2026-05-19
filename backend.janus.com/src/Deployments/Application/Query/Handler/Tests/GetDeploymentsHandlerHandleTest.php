<?php

/**
 * @file GetDeploymentsHandlerHandleTest.php
 *
 * Tests for GetDeploymentsHandler::handle().
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Application\Query\GetDeploymentsQuery;
use App\Deployments\Application\Query\Handler\GetDeploymentsHandler;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that handle() returns paginated DTOs with the correct total count.
 */
#[CoversClass(className: GetDeploymentsHandler::class)]
final class GetDeploymentsHandlerHandleTest extends GetDeploymentsHandlerTest
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
     * Test that handle() returns a "data" key with DeploymentProviderDto instances.
     */
    public function testHandleReturnsDataArray(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeProvider()]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->class->handle(new GetDeploymentsQuery(10, 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(DeploymentProviderDto::class, $result['data'][0]);
    }

    /**
     * Test that handle() returns the correct total count.
     */
    public function testHandleReturnsTotalCount(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->class->handle(new GetDeploymentsQuery(10, 0));

        $this->assertSame(42, $result['total']);
    }

    /**
     * Test that handle() returns empty data when there are no providers.
     */
    public function testHandleReturnsEmptyDataWhenNoProviders(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetDeploymentsQuery(10, 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }
}
