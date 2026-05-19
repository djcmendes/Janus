<?php

/**
 * @file ActivityEntitySetTimestampTest.php
 *
 * Tests for ActivityEntity::setTimestamp() and ActivityEntity::timestamp { get; set }.
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Test class for ActivityEntity::setTimestamp() — storing and retrieving the timestamp.
 */
#[CoversClass(className:  ActivityEntity::class)]
#[CoversMethod(className: ActivityEntity::class, methodName: 'setTimestamp')]
final class ActivityEntitySetTimestampTest extends ActivityEntityTest
{
    /**
     * Test that setTimestamp() stores the values.
     */
    public function testSetTimestampStoresValue(): void
    {
        $ts = new DateTimeImmutable(datetime: '2024-06-15T12:00:00+00:00');
        $this->class->setTimestamp(timestamp: $ts);

        $this->assertSame(expected: $ts, actual: $this->class->timestamp);
    }

    /**
     * Test that setTimestamp() returns static instance
     */
    public function testSetTimestampReturnsStaticInstance(): void
    {
        $result = $this->class->setTimestamp(timestamp: new DateTimeImmutable());

        $this->assertSame(expected: $this->class, actual: $result);
    }

    /**
     * Test that setTimestamp() overwrites previous value
     */
    public function testSetTimestampOverwritesPreviousValue(): void
    {
        $first  = new DateTimeImmutable(datetime: '2024-01-01T00:00:00+00:00');
        $second = new DateTimeImmutable(datetime: '2024-06-01T00:00:00+00:00');

        $this->class->setTimestamp(timestamp: $first);
        $this->class->setTimestamp(timestamp: $second);

        $this->assertSame(expected: $second, actual: $this->class->timestamp);
    }
}
