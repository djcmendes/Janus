<?php

/**
 * @file DashboardSetIconTest.php
 *
 * Tests for Dashboard::setIcon().
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies setIcon() updates the icon identifier and refreshes the updatedAt timestamp.
 */
#[CoversClass(className: Dashboard::class)]
final class DashboardSetIconTest extends DashboardTest
{
    /**
     * Test that setIcon() returns the same Dashboard instance (fluent interface).
     */
    public function testSetIconReturnsSelf(): void
    {
        $result = $this->class->setIcon('chart');

        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setIcon() stores the new icon value.
     */
    public function testSetIconChangesIcon(): void
    {
        $this->class->setIcon('chart-bar');

        $this->assertSame('chart-bar', $this->class->getIcon());
    }

    /**
     * Test that setIcon(null) clears the icon.
     */
    public function testSetIconNullClearsIcon(): void
    {
        $this->class->setIcon(null);

        $this->assertNull($this->class->getIcon());
    }

    /**
     * Test that setIcon() refreshes the updatedAt timestamp.
     */
    public function testSetIconRefreshesUpdatedAt(): void
    {
        $before = $this->class->getUpdatedAt();

        usleep(1000);
        $this->class->setIcon('new-icon');

        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
    }
}
