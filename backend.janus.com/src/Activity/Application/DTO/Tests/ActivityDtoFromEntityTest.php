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

#[CoversClass(ActivityDto::class)]
#[CoversMethod(ActivityDto::class, 'fromEntity')]
final class ActivityDtoFromEntityTest extends ActivityDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testFromEntityMapsId(): void
    {
        $activity = $this->makeActivity();
        $dto      = ActivityDto::fromEntity($activity);

        $this->assertSame((string) $activity->getId(), $dto->id);
    }

    public function testFromEntityMapsAction(): void
    {
        $this->assertSame('create', $this->class->action);
    }

    public function testFromEntityMapsCollection(): void
    {
        $this->assertSame('posts', $this->class->collection);
    }

    public function testFromEntityMapsItem(): void
    {
        $this->assertSame('42', $this->class->item);
    }

    public function testFromEntityMapsUserId(): void
    {
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $this->class->userId);
    }

    public function testFromEntityMapsIp(): void
    {
        $this->assertSame('127.0.0.1', $this->class->ip);
    }

    public function testFromEntityMapsUserAgent(): void
    {
        $this->assertSame('PHPUnit', $this->class->userAgent);
    }

    public function testFromEntityMapsTimestampAsAtomString(): void
    {
        $activity = $this->makeActivity();
        $dto      = ActivityDto::fromEntity($activity);

        $this->assertSame(
            $activity->getTimestamp()->format(DateTimeInterface::ATOM),
            $dto->timestamp,
        );
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testFromEntityMapsNullCollectionAndItem(): void
    {
        $dto = ActivityDto::fromEntity(new Activity('login'));

        $this->assertNull($dto->collection);
        $this->assertNull($dto->item);
    }

    public function testFromEntityMapsNullUserId(): void
    {
        $dto = ActivityDto::fromEntity(new Activity('login'));

        $this->assertNull($dto->userId);
    }

    public function testFromEntityMapsNullIpAndUserAgent(): void
    {
        $dto = ActivityDto::fromEntity(new Activity('login'));

        $this->assertNull($dto->ip);
        $this->assertNull($dto->userAgent);
    }
}
