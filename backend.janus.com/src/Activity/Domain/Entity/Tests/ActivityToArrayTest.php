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

#[CoversClass(Activity::class)]
#[CoversMethod(Activity::class, 'toArray')]
final class ActivityToArrayTest extends ActivityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->makeActivity()->toArray();

        foreach (['id', 'action', 'collection', 'item', 'user', 'ip', 'user_agent', 'timestamp'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    public function testToArrayIncludesIdAsString(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertIsString($array['id']);
        $this->assertSame($activity->getId(), $array['id']);
    }

    public function testToArrayFormatsTimestampAsAtomString(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame(
            $activity->getTimestamp()->format(DateTimeInterface::ATOM),
            $array['timestamp'],
        );
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToArrayMapsUserIdToUserKey(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $array['user']);
        $this->assertArrayNotHasKey('userId', $array);
    }

    public function testToArrayMapsUserAgentToSnakeCaseKey(): void
    {
        $activity = $this->makeActivity();
        $array    = $activity->toArray();

        $this->assertSame('PHPUnit', $array['user_agent']);
        $this->assertArrayNotHasKey('userAgent', $array);
    }

    public function testToArrayNullableFieldsAreNullWhenNotSet(): void
    {
        $array = (new Activity('login'))->toArray();

        $this->assertNull($array['collection']);
        $this->assertNull($array['item']);
        $this->assertNull($array['user']);
        $this->assertNull($array['ip']);
        $this->assertNull($array['user_agent']);
    }
}
