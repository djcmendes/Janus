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

#[CoversClass(ActivityDto::class)]
#[CoversMethod(ActivityDto::class, 'toArray')]
final class ActivityDtoToArrayTest extends ActivityDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        foreach (['id', 'action', 'collection', 'item', 'user', 'ip', 'user_agent', 'timestamp'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    public function testToArrayValuesMatchDtoProperties(): void
    {
        $array = $this->class->toArray();

        $this->assertSame($this->class->id, $array['id']);
        $this->assertSame($this->class->action, $array['action']);
        $this->assertSame($this->class->timestamp, $array['timestamp']);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testToArrayMapsUserIdToUserKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $array['user']);
        $this->assertArrayNotHasKey('userId', $array);
    }

    public function testToArrayMapsUserAgentToSnakeCaseKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame('PHPUnit', $array['user_agent']);
        $this->assertArrayNotHasKey('userAgent', $array);
    }

    public function testToArrayNullableFieldsAreNull(): void
    {
        $dto   = ActivityDto::fromEntity(new Activity('login'));
        $array = $dto->toArray();

        $this->assertNull($array['collection']);
        $this->assertNull($array['item']);
        $this->assertNull($array['user']);
        $this->assertNull($array['ip']);
        $this->assertNull($array['user_agent']);
    }
}
