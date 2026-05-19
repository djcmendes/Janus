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
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that reconstitute() uses the provided id instead of generating one.
     */
    public function testReconstituteUsesSuppliedId(): void
    {
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'create', 'posts', '1', null, null, null,
            new DateTimeImmutable(),
        );

        $this->assertSame(self::FIXED_UUID, $activity->id);
    }

    /**
     * Test that reconstitute() uses the provided timestamp instead of generating one.
     */
    public function testReconstituteUsesSuppliedTimestamp(): void
    {
        $ts       = new DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'create', null, null, null, null, null, $ts,
        );

        $this->assertSame($ts, $activity->timestamp);
    }

    /**
     * Test that reconstitute() populates all fields when all arguments are provided.
     */
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

    /**
     * Test that reconstitute() accepts null for all optional fields.
     */
    public function testReconstituteAcceptsNullForAllOptionalFields(): void
    {
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'login', null, null, null, null, null,
            new DateTimeImmutable(),
        );

        $this->assertNull($activity->collection);
        $this->assertNull($activity->item);
        $this->assertNull($activity->userId);
        $this->assertNull($activity->ip);
        $this->assertNull($activity->userAgent);
    }
}
