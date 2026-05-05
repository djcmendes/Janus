<?php

/**
 * @file ActivityBaseTest.php
 *
 * Constructor and interface compliance tests for the Activity domain entity.
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Activity::class)]
final class ActivityBaseTest extends ActivityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfActivity(): void
    {
        $this->assertInstanceOf(Activity::class, $this->class);
    }

    public function testConstructorSetsAction(): void
    {
        $this->assertSame('create', $this->class->getAction());
    }

    public function testConstructorSetsCollection(): void
    {
        $this->assertSame('posts', $this->class->getCollection());
    }

    public function testConstructorSetsItem(): void
    {
        $this->assertSame('42', $this->class->getItem());
    }

    public function testConstructorGeneratesUuidV7String(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $this->class->getId(),
        );
    }

    public function testConstructorSetsTimestampToApproximatelyNow(): void
    {
        $before   = new \DateTimeImmutable();
        $activity = new Activity('create');
        $after    = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $activity->getTimestamp());
        $this->assertLessThanOrEqual($after, $activity->getTimestamp());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testConstructorDefaultsCollectionToNull(): void
    {
        $activity = new Activity('login');

        $this->assertNull($activity->getCollection());
    }

    public function testConstructorDefaultsItemToNull(): void
    {
        $activity = new Activity('login');

        $this->assertNull($activity->getItem());
    }

    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new Activity('create');
        $b = new Activity('create');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
