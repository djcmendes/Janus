<?php

/**
 * @file DeploymentProviderRepositoryFindByIdTest.php
 *
 * Tests for DeploymentProviderRepository::findById().
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
 * Verifies that findById() returns a mapped domain entity or null.
 */
#[CoversClass(DeploymentProviderRepository::class)]
final class DeploymentProviderRepositoryFindByIdTest extends DeploymentProviderRepositoryTest
{
    /**
     * Test that findById() returns a DeploymentProvider when the entity is found.
     */
    public function testFindByIdReturnsDomainEntityWhenFound(): void
    {
        $entity = $this->makeProviderEntity();
        $this->entityManager->method('find')->willReturn($entity);

        $result = $this->class->findById('aaaaaaaa-0000-7000-8000-000000000001');

        $this->assertInstanceOf(DeploymentProvider::class, $result);
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->getId());
    }

    /**
     * Test that findById() returns null when no entity is found.
     */
    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $result = $this->class->findById('nonexistent-id');

        $this->assertNull($result);
    }
}
