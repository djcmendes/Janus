<?php

/**
 * @file DashboardRepositorySaveTest.php
 *
 * Tests for DashboardRepository::save().
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies save() persists a Dashboard domain entity via the entity manager.
 */
#[CoversClass(DashboardRepository::class)]
final class DashboardRepositorySaveTest extends DashboardRepositoryTest
{
    /**
     * Test that save() calls persist() on the entity manager.
     */
    public function testSaveCallsPersist(): void
    {
        $this->entityManager->expects($this->once())->method('persist');

        $this->class->save($this->makeDashboard());
    }

    /**
     * Test that save() calls flush() on the entity manager.
     */
    public function testSaveCallsFlush(): void
    {
        $this->entityManager->expects($this->once())->method('flush');

        $this->class->save($this->makeDashboard());
    }

    /**
     * Test that save() returns void.
     */
    public function testSaveReturnsVoid(): void
    {
        $result = $this->class->save($this->makeDashboard());

        $this->assertNull($result);
    }
}
