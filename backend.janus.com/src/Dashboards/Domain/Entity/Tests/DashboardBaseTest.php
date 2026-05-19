<?php

/**
 * @file DashboardBaseTest.php
 *
 * Constructor and interface compliance tests for the Dashboard domain entity.
 *
 * @package App\Dashboards\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Constructor and interface compliance tests for the Dashboard domain entity.
 */
#[CoversClass(className: Dashboard::class)]
final class DashboardBaseTest extends DashboardTest
{
    /**
     * Test that the SUT is an instance of Dashboard.
     */
    public function testIsInstanceOfDashboard(): void
    {
        $this->assertInstanceOf(Dashboard::class, $this->class);
    }

    /**
     * Test that the constructor generates a valid UUIDv7 string.
     */
    public function testConstructorGeneratesUuidV7(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $this->class->getId(),
        );
    }

    /**
     * Test that the constructor stores the name argument.
     */
    public function testConstructorSetsName(): void
    {
        $this->assertSame('My Dashboard', $this->class->getName());
    }

    /**
     * Test that the constructor stores the icon argument.
     */
    public function testConstructorSetsIcon(): void
    {
        $this->assertSame('dashboard', $this->class->getIcon());
    }

    /**
     * Test that the constructor stores the note argument.
     */
    public function testConstructorSetsNote(): void
    {
        $this->assertSame('A note', $this->class->getNote());
    }

    /**
     * Test that the constructor stores the userId argument.
     */
    public function testConstructorSetsUserId(): void
    {
        $this->assertSame('user-uuid-001', $this->class->getUserId());
    }

    /**
     * Test that icon defaults to null when omitted.
     */
    public function testIconDefaultsToNull(): void
    {
        $d = new Dashboard('Minimal');
        $this->assertNull($d->getIcon());
    }

    /**
     * Test that note defaults to null when omitted.
     */
    public function testNoteDefaultsToNull(): void
    {
        $d = new Dashboard('Minimal');
        $this->assertNull($d->getNote());
    }

    /**
     * Test that userId defaults to null when omitted.
     */
    public function testUserIdDefaultsToNull(): void
    {
        $d = new Dashboard('Minimal');
        $this->assertNull($d->getUserId());
    }

    /**
     * Test that createdAt is set to approximately the current time.
     */
    public function testConstructorSetsCreatedAtToNow(): void
    {
        $before = new \DateTimeImmutable();
        $d      = new Dashboard('Now');
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $d->getCreatedAt());
        $this->assertLessThanOrEqual($after, $d->getCreatedAt());
    }

    /**
     * Test that updatedAt equals createdAt on a freshly constructed instance.
     */
    public function testConstructorSetsUpdatedAtEqualToCreatedAt(): void
    {
        $d = new Dashboard('Fresh');
        $this->assertEqualsWithDelta(
            $d->getCreatedAt()->getTimestamp(),
            $d->getUpdatedAt()->getTimestamp(),
            1,
        );
    }

    /**
     * Test that each instance receives a unique UUID.
     */
    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new Dashboard('A');
        $b = new Dashboard('B');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
