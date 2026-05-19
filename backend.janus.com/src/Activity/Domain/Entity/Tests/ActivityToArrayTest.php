<?php

/**
 * @file ActivityToArrayTest.php
 *
 * Tests for Activity::toArray().
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for Activity::toArray() — verifying the serialised representation.
 */
#[CoversClass(className:  Activity::class)]
#[CoversMethod(className: Activity::class, methodName: 'toArray')]
final class ActivityToArrayTest extends ActivityTest
{
    /**
     * Test that toArray() includes all expected keys.
     */
    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->makeActivity()->toArray();

        foreach (['id', 'action', 'collection', 'item', 'user', 'ip', 'user_agent', 'timestamp'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * Test that toArray() includes the entity UUID as a string under the 'id' key.
     */
    public function testToArrayIncludesIdAsString(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertIsString($array['id']);
        $this->assertSame($activity->id, $array['id']);
    }

    /**
     * Test that toArray() formats the timestamp as an ATOM-format string.
     */
    public function testToArrayFormatsTimestampAsAtomString(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame(
            $activity->timestamp->format(DateTimeInterface::ATOM),
            $array['timestamp'],
        );
    }

    /**
     * Test that toArray() maps userId to 'user' and omits 'userId'.
     */
    public function testToArrayMapsUserIdToUserKey(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $array['user']);
        $this->assertArrayNotHasKey('userId', $array);
    }

    /**
     * Test that toArray() maps userAgent to 'user_agent' and omits 'userAgent'.
     */
    public function testToArrayMapsUserAgentToSnakeCaseKey(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame('PHPUnit', $array['user_agent']);
        $this->assertArrayNotHasKey('userAgent', $array);
    }

    /**
     * Test that nullable fields are null in toArray() when not explicitly set.
     */
    public function testToArrayNullableFieldsAreNullWhenNotSet(): void
    {
        $array = (new Activity('login'))->toArray();

        $this->assertNull(actual: $array['collection']);
        $this->assertNull(actual: $array['item']);
        $this->assertNull(actual: $array['user']);
        $this->assertNull(actual: $array['ip']);
        $this->assertNull(actual: $array['user_agent']);
    }
}
