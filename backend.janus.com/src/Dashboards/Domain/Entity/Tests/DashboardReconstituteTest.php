<?php

/**
 * @file DashboardReconstituteTest.php
 *
 * Tests for the Dashboard::reconstitute() static factory.
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that reconstitute() rebuilds a Dashboard from persistence data without side effects.
 */
#[CoversClass(className: Dashboard::class)]
final class DashboardReconstituteTest extends DashboardTest
{
    /**
     * Test that reconstitute() preserves the provided ID without generating a new one.
     */
    public function testReconstitutePreservesId(): void
    {
        $dashboard = $this->makeReconstituted(id: 'aaaaaaaa-0000-7000-8000-000000000099');

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000099', $dashboard->getId());
    }

    /**
     * Test that reconstitute() sets the name.
     */
    public function testReconstituteSetsName(): void
    {
        $dashboard = $this->makeReconstituted(name: 'Restored Dashboard');

        $this->assertSame('Restored Dashboard', $dashboard->getName());
    }

    /**
     * Test that reconstitute() sets a null icon.
     */
    public function testReconstituteSetsNullIcon(): void
    {
        $dashboard = $this->makeReconstituted(icon: null);

        $this->assertNull($dashboard->getIcon());
    }

    /**
     * Test that reconstitute() sets a non-null icon.
     */
    public function testReconstituteSetsNonNullIcon(): void
    {
        $dashboard = $this->makeReconstituted(icon: 'chart');

        $this->assertSame('chart', $dashboard->getIcon());
    }

    /**
     * Test that reconstitute() preserves the original createdAt timestamp.
     */
    public function testReconstitutePreservesCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2020-01-01T12:00:00Z');
        $dashboard = $this->makeReconstituted(createdAt: $createdAt);

        $this->assertSame($createdAt, $dashboard->getCreatedAt());
    }

    /**
     * Test that reconstitute() preserves the original updatedAt timestamp.
     */
    public function testReconstitutePreservesUpdatedAt(): void
    {
        $updatedAt = new \DateTimeImmutable('2022-06-15T08:30:00Z');
        $dashboard = $this->makeReconstituted(updatedAt: $updatedAt);

        $this->assertSame($updatedAt, $dashboard->getUpdatedAt());
    }

    /**
     * Test that reconstitute() with a null userId produces a shared dashboard.
     */
    public function testReconstituteWithNullUserIdProducesSharedDashboard(): void
    {
        $dashboard = $this->makeReconstituted(userId: null);

        $this->assertNull($dashboard->getUserId());
    }
}
