<?php

/**
 * @file ActivityReconstituteTest.php
 *
 * Tests for Activity::reconstitute().
 *
 * @package App\Activity\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Domain\Entity\Tests;

use App\Activity\Domain\Entity\Activity;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for Activity::reconstitute() — hydrating an entity from persisted state.
 */
#[CoversClass(className:  Activity::class)]
#[CoversMethod(className: Activity::class, methodName: 'reconstitute')]
final class ActivityReconstituteTest extends ActivityTest
{
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    public function testReconstituteUsesSuppliedId(): void
    {
        $activity = Activity::reconstitute(
            id:         self::FIXED_UUID,
            action:     'create',
            collection: 'posts',
            item:       '1',
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  new DateTimeImmutable(),
        );

        $this->assertSame(self::FIXED_UUID, $activity->id);
    }

    public function testReconstituteUsesSuppliedTimestamp(): void
    {
        $ts       = new DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $activity = Activity::reconstitute(
            id:         self::FIXED_UUID,
            action:     'create',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  $ts,
        );

        $this->assertSame($ts, $activity->timestamp);
    }

    public function testReconstitutePopulatesAllFields(): void
    {
        $activity = Activity::reconstitute(
            id:         self::FIXED_UUID,
            action:     'delete',
            collection: 'articles',
            item:       '99',
            userId:     'user-uuid',
            ip:         '10.0.0.1',
            userAgent:  'Bot/1.0',
            timestamp:  new DateTimeImmutable(),
        );

        $this->assertSame('delete', $activity->action);
        $this->assertSame('articles', $activity->collection);
        $this->assertSame('99', $activity->item);
        $this->assertSame('user-uuid', $activity->userId);
        $this->assertSame('10.0.0.1', $activity->ip);
        $this->assertSame('Bot/1.0', $activity->userAgent);
    }

    public function testReconstituteAcceptsNullForAllOptionalFields(): void
    {
        $activity = Activity::reconstitute(
            id:         self::FIXED_UUID,
            action:     'login',
            collection: null,
            item:       null,
            userId:     null,
            ip:         null,
            userAgent:  null,
            timestamp:  new DateTimeImmutable(),
        );

        $this->assertNull($activity->collection);
        $this->assertNull($activity->item);
        $this->assertNull($activity->userId);
        $this->assertNull($activity->ip);
        $this->assertNull($activity->userAgent);
    }
}
