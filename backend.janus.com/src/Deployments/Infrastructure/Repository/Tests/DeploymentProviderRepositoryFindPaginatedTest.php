<?php

/**
 * @file DeploymentProviderRepositoryFindPaginatedTest.php
 *
 * Tests for DeploymentProviderRepository::findPaginated().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Infrastructure\Repository\DeploymentProviderRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that findPaginated() returns an array of mapped domain entities.
 */
#[CoversClass(DeploymentProviderRepository::class)]
final class DeploymentProviderRepositoryFindPaginatedTest extends DeploymentProviderRepositoryTest
{
    /**
     * Test that findPaginated() returns an array of DeploymentProvider instances.
     */
    public function testFindPaginatedReturnsArrayOfProviders(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeProviderEntity()]);

        $results = $this->class->findPaginated(10, 0);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(DeploymentProvider::class, $results[0]);
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
}
