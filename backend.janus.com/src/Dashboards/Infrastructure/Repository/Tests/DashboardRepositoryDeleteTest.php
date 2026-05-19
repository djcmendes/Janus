<?php

/**
 * @file DashboardRepositoryDeleteTest.php
 *
 * Tests for DashboardRepository::delete().
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies delete() removes a Dashboard when the entity is found by its ID.
 */
#[CoversClass(className: DashboardRepository::class)]
final class DashboardRepositoryDeleteTest extends DashboardRepositoryTest
{
    /**
     * Test that delete() calls remove() and flush() when the entity is found.
     */
    public function testDeleteCallsRemoveAndFlushWhenFound(): void
    {
        $entity = $this->makeDashboardEntity();

        $this->entityManager->method('find')->willReturn($entity);
        $this->entityManager->expects($this->once())->method('remove')->with($entity);
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->delete($this->makeDashboard());
    }

    /**
     * Test that delete() does not call remove() or flush() when the entity is not found.
     */
    public function testDeleteDoesNothingWhenEntityNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);
        $this->entityManager->expects($this->never())->method('remove');
        $this->entityManager->expects($this->never())->method('flush');

        $this->class->delete($this->makeDashboard());
    }
}
