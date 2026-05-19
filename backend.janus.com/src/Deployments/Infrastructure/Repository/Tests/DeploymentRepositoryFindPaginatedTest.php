<?php

/**
 * @file DeploymentRepositoryFindPaginatedTest.php
 *
 * Tests for DeploymentRepository::findPaginated().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Infrastructure\Repository\DeploymentRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that findPaginated() returns an array of mapped domain entities.
 */
#[CoversClass(className: DeploymentRepository::class)]
final class DeploymentRepositoryFindPaginatedTest extends DeploymentRepositoryTest
{
    /**
     * Test that findPaginated() returns an array of Deployment instances.
     */
    public function testFindPaginatedReturnsArrayOfDeployments(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeDeploymentEntity()]);

        $results = $this->class->findPaginated(10, 0);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(Deployment::class, $results[0]);
    }

    /**
     * Test that findPaginated() returns an empty array when there are no results.
     */
    public function testFindPaginatedReturnsEmptyArrayWhenNoResults(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $results = $this->class->findPaginated(10, 0);

        $this->assertSame([], $results);
    }

    /**
     * Test that findPaginated() applies providerId filter when given.
     */
    public function testFindPaginatedAppliesProviderIdFilter(): void
    {
        $this->queryBuilder->expects($this->once())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, 'pppppppp-0000-7000-8000-000000000001');
    }

    /**
     * Test that findPaginated() does not filter when providerId is null.
     */
    public function testFindPaginatedDoesNotFilterWhenProviderIdIsNull(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, null);
    }
}
