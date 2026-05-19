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
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Constructor and interface compliance tests for the Activity domain entity.
 */
#[CoversClass(className:  Activity::class)]
final class ActivityBaseTest extends ActivityTest
{
    /**
     * Test that the SUT is an instance of Activity.
     */
    public function testIsInstanceOfActivity(): void
    {
        $this->assertInstanceOf(expected: Activity::class, actual: $this->class);
    }

    /**
     * Test that the constructor stores the action argument.
     */
    public function testConstructorSetsAction(): void
    {
        $this->assertSame(expected: 'create', actual: $this->class->getAction());
    }

    /**
     * Test that the constructor stores the collection argument.
     */
    public function testConstructorSetsCollection(): void
    {
        $this->assertSame(expected: 'posts', actual: $this->class->getCollection());
    }

    /**
     * Test that the constructor stores the item argument.
     */
    public function testConstructorSetsItem(): void
    {
        $this->assertSame(expected: '42', actual: $this->class->getItem());
    }

    /**
     * Test that the constructor generates a valid UUIDv7 string.
     */
    public function testConstructorGeneratesUuidV7String(): void
    {
        $this->assertMatchesRegularExpression(
            pattern: '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            string:  $this->class,
        );
    }

    /**
     * Test that the constructor sets the timestamp to approximately the current time.
     */
    public function testConstructorSetsTimestampToApproximatelyNow(): void
    {
        $before   = new DateTimeImmutable();
        $activity = new Activity(action: 'create');
        $after    = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual(minimum: $before, actual: $activity->timestamp);
        $this->assertLessThanOrEqual(maximum: $after, actual: $activity->timestamp);
    }

    /**
     * Test that the collection property defaults to null when omitted.
     */
    public function testConstructorDefaultsCollectionToNull(): void
    {
        $activity = new Activity(action: 'login');

        $this->assertNull(actual: $activity->getCollection());
    }

    /**
     * Test that the item property defaults to null when omitted.
     */
    public function testConstructorDefaultsItemToNull(): void
    {
        $activity = new Activity(action: 'login');

        $this->assertNull(actual: $activity->getItem());
    }

    /**
     * Test that each Activity instance receives a unique UUID.
     */
    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new Activity(action: 'create');
        $b = new Activity(action: 'create');

        $this->assertNotSame(expected: $a->id, actual: $b->id);
    }
}
