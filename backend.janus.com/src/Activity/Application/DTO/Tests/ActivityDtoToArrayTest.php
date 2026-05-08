<?php

/**
 * @file ActivityDtoToArrayTest.php
 *
 * Tests for ActivityDto::toArray().
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Domain\Entity\Activity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that toArray() serialises every ActivityDto property into the
 * correct snake_case key and that nullable fields are preserved as null.
 */
#[CoversClass(ActivityDto::class)]
#[CoversMethod(ActivityDto::class, 'toArray')]
final class ActivityDtoToArrayTest extends ActivityDtoTest
{
    /**
     * Test that toArray() returns an array containing all eight expected keys.
     */
    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        foreach (['id', 'action', 'collection', 'item', 'user', 'ip', 'user_agent', 'timestamp'] as $key) {
            $this->assertArrayHasKey(key: $key, array: $array);
        }
    }

    /**
     * Test that the id, action, and timestamp values in the array match the corresponding DTO properties.
     */
    public function testToArrayValuesMatchDtoProperties(): void
    {
        $array = $this->class->toArray();

        $this->assertSame(expected: $this->class->id, actual: $array['id']);
        $this->assertSame(expected: $this->class->action, actual: $array['action']);
        $this->assertSame(expected: $this->class->timestamp, actual: $array['timestamp']);
    }

    /**
     * Test that toArray() maps the userId property to the 'user' key and omits 'userId'.
     */
    public function testToArrayMapsUserIdToUserKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame(expected: 'bbbbbbbb-0000-7000-8000-000000000002', actual: $array['user']);
        $this->assertArrayNotHasKey(key: 'userId', array: $array);
    }

    /**
     * Test that toArray() maps the userAgent property to the 'user_agent' key and omits 'userAgent'.
     */
    public function testToArrayMapsUserAgentToSnakeCaseKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame(expected: 'PHPUnit', actual: $array['user_agent']);
        $this->assertArrayNotHasKey(key: 'userAgent', array: $array);
    }

    /**
     * Test that toArray() stores null for all nullable fields when the Activity carries no optional data.
     */
    public function testToArrayNullableFieldsAreNull(): void
    {
        $dto   = ActivityDto::fromEntity(a: new Activity(action: 'login'));
        $array = $dto->toArray();

        $this->assertNull(actual: $array['collection']);
        $this->assertNull(actual: $array['item']);
        $this->assertNull(actual: $array['user']);
        $this->assertNull(actual: $array['ip']);
        $this->assertNull(actual: $array['user_agent']);
    }
}
