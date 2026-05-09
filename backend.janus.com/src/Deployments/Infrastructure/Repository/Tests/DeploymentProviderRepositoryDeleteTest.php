<?php

/**
 * @file DeploymentProviderRepositoryDeleteTest.php
 *
 * Tests for DeploymentProviderRepository::delete().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentProviderRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that delete() removes a provider and flushes, or does nothing when not found.
 */
#[CoversClass(DeploymentProviderRepository::class)]
final class DeploymentProviderRepositoryDeleteTest extends DeploymentProviderRepositoryTest
{
    /**
     * Test that delete() calls remove() and flush() when the entity is found.
     */
    public function testDeleteCallsRemoveAndFlushWhenEntityFound(): void
    {
        $entity = $this->makeProviderEntity();
        $this->entityManager->method('find')->willReturn($entity);
        $this->entityManager->expects($this->once())->method('remove')->with($entity);
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($this->makeProvider());
    }

    /**
     * Test that delete() does nothing when the entity is not found.
     */
    public function testDeleteDoesNothingWhenEntityNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->never())->method('remove');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->delete($this->makeProvider());
    }
}
