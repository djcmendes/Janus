<?php

/**
 * @file DashboardSetNameTest.php
 *
 * Tests for Dashboard::setName().
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies setName() updates the name and refreshes the updatedAt timestamp.
 */
#[CoversClass(Dashboard::class)]
final class DashboardSetNameTest extends DashboardTest
{
    /**
     * Test that setName() returns the same Dashboard instance (fluent interface).
     */
    public function testSetNameReturnsSelf(): void
    {
        $result = $this->class->setName('New Name');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setName() changes the stored name.
     */
    public function testSetNameChangesName(): void
    {
        $this->class->setName('Updated Name');

        $this->assertSame('Updated Name', $this->class->getName());
    }

    /**
     * Test that setName() refreshes the updatedAt timestamp.
     */
    public function testSetNameRefreshesUpdatedAt(): void
    {
        $before = $this->class->getUpdatedAt();

        // Ensure measurable time difference
        usleep(1000);
        $this->class->setName('New Name');

        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
    }
}
