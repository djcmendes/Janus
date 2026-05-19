<?php

/**
 * @file DashboardIsOwnedByTest.php
 *
 * Tests for Dashboard::isOwnedBy().
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies isOwnedBy() correctly compares the dashboard owner against a given user UUID.
 */
#[CoversClass(className: Dashboard::class)]
final class DashboardIsOwnedByTest extends DashboardTest
{
    /**
     * Test that isOwnedBy() returns true for the dashboard's own userId.
     */
    public function testIsOwnedByReturnsTrueForOwner(): void
    {
        $this->assertTrue($this->class->isOwnedBy('user-uuid-001'));
    }

    /**
     * Test that isOwnedBy() returns false for a different userId.
     */
    public function testIsOwnedByReturnsFalseForOtherUser(): void
    {
        $this->assertFalse($this->class->isOwnedBy('user-uuid-999'));
    }

    /**
     * Test that isOwnedBy() returns false when the dashboard has no owner (shared).
     */
    public function testIsOwnedByReturnsFalseWhenShared(): void
    {
        $shared = new Dashboard('Shared', null, null, null);

        $this->assertFalse($shared->isOwnedBy('user-uuid-001'));
    }
}
