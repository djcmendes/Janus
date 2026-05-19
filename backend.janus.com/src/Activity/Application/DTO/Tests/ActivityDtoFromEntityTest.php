<?php

/**
 * @file ActivityDtoFromEntityTest.php
 *
 * Tests for ActivityDto::fromEntity().
 *
 * @package App\Activity\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\DTO\Tests;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Domain\Entity\Activity;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * This class contains tests for the ActivityDto class.
 */
#[CoversClass(className:  ActivityDto::class)]
#[CoversMethod(className: ActivityDto::class, methodName: 'fromEntity')]
final class ActivityDtoFromEntityTest extends ActivityDtoTest
{
    /**
     * Test that fromEntity() maps the Activity UUID string to the id property.
     */
    public function testFromEntityMapsId(): void
    {
        $activity = $this->makeActivity();
        $dto      = ActivityDto::fromEntity(activity: $activity);

        $this->assertSame(expected: (string)$activity->id, actual: $dto->id);
    }

    /**
     * Test that fromEntity() maps the action field to the action property.
     */
    public function testFromEntityMapsAction(): void
    {
        $this->assertSame(expected: 'create', actual: $this->class->action);
    }

    /**
     * Test that fromEntity() maps the collection field to the collection property.
     */
    public function testFromEntityMapsCollection(): void
    {
        $this->assertSame(expected: 'posts', actual: $this->class->collection);
    }

    /**
     * Test that fromEntity() maps the item identifier to the item property.
     */
    public function testFromEntityMapsItem(): void
    {
        $this->assertSame(expected: '42', actual: $this->class->item);
    }

    /**
     * Test that fromEntity() maps the userId field to the userId property.
     */
    public function testFromEntityMapsUserId(): void
    {
        $this->assertSame(expected: 'bbbbbbbb-0000-7000-8000-000000000002', actual: $this->class->userId);
    }

    /**
     * Test that fromEntity() maps the ip address to the ip property.
     */
    public function testFromEntityMapsIp(): void
    {
        $this->assertSame(expected: '127.0.0.1', actual: $this->class->ip);
    }

    /**
     * Test that fromEntity() maps the userAgent string to the userAgent property.
     */
    public function testFromEntityMapsUserAgent(): void
    {
        $this->assertSame(expected: 'PHPUnit', actual: $this->class->userAgent);
    }

    /**
     * Test that fromEntity() formats the Activity timestamp as an ATOM string on the timestamp property.
     */
    public function testFromEntityMapsTimestampAsAtomString(): void
    {
        $activity = $this->makeActivity();
        $dto      = ActivityDto::fromEntity(activity: $activity);

        $this->assertSame(
            expected: $activity->timestamp->format(format: DateTimeInterface::ATOM),
            actual:   $dto->timestamp,
        );
    }

    /**
     * Test that fromEntity() stores null on collection and item when the Activity has neither.
     */
    public function testFromEntityMapsNullCollectionAndItem(): void
    {
        $dto = ActivityDto::fromEntity(activity: new Activity(action: 'login'));

        $this->assertNull(actual: $dto->collection);
        $this->assertNull(actual: $dto->item);
    }

    /**
     * Test that fromEntity() stores null on userId when no user is associated with the Activity.
     */
    public function testFromEntityMapsNullUserId(): void
    {
        $dto = ActivityDto::fromEntity(activity: new Activity(action: 'login'));

        $this->assertNull(actual: $dto->userId);
    }

    /**
     * Test that fromEntity() stores null on ip and userAgent when neither was set on the Activity.
     */
    public function testFromEntityMapsNullIpAndUserAgent(): void
    {
        $dto = ActivityDto::fromEntity(activity: new Activity(action: 'login'));

        $this->assertNull(actual: $dto->ip);
        $this->assertNull(actual: $dto->userAgent);
    }
}
