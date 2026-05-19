<?php

/**
 * @file DeploymentRepositorySaveTest.php
 *
 * Tests for DeploymentRepository::save().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that save() persists and flushes a Deployment domain entity.
 */
#[CoversClass(className: DeploymentRepository::class)]
final class DeploymentRepositorySaveTest extends DeploymentRepositoryTest
{
    /**
     * Test that save() calls persist() and flush() on the entity manager.
     */
    public function testSaveCallsPersistAndFlush(): void
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($this->makeDeployment());
    }

    /**
     * Test that save() does not flush when $flush is false.
     */
    public function testSaveSkipsFlushWhenFlagFalse(): void
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->save($this->makeDeployment(), false);
    }
}
