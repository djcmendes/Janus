<?php

/**
 * @file DeploymentRepositoryFindByIdTest.php
 *
 * Tests for DeploymentRepository::findById().
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
 * Verifies that findById() returns a mapped domain entity or null.
 */
#[CoversClass(className: DeploymentRepository::class)]
final class DeploymentRepositoryFindByIdTest extends DeploymentRepositoryTest
{
    /**
     * Test that findById() returns a Deployment when the entity is found.
     */
    public function testFindByIdReturnsDomainEntityWhenFound(): void
    {
        $entity = $this->makeDeploymentEntity();
        $this->entityManager->method('find')->willReturn($entity);

        $result = $this->class->findById('aaaaaaaa-0000-7000-8000-000000000001');

        $this->assertInstanceOf(Deployment::class, $result);
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
