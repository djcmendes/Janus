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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(Activity::class)]
#[CoversMethod(Activity::class, 'reconstitute')]
final class ActivityReconstituteTest extends ActivityTest
{
    /** @var string */
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    public function testReconstituteUsesSuppliedId(): void
    {
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'create', 'posts', '1', null, null, null,
            new \DateTimeImmutable(),
        );

        $this->assertSame(self::FIXED_UUID, $activity->getId());
    }

    public function testReconstituteUsesSuppliedTimestamp(): void
    {
        $ts       = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'create', null, null, null, null, null, $ts,
        );

        $this->assertSame($ts, $activity->getTimestamp());
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
            timestamp:  new \DateTimeImmutable(),
        );

        $this->assertSame('delete', $activity->getAction());
        $this->assertSame('articles', $activity->getCollection());
        $this->assertSame('99', $activity->getItem());
        $this->assertSame('user-uuid', $activity->getUserId());
        $this->assertSame('10.0.0.1', $activity->getIp());
        $this->assertSame('Bot/1.0', $activity->getUserAgent());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testReconstituteAcceptsNullForAllOptionalFields(): void
    {
        $activity = Activity::reconstitute(
            self::FIXED_UUID, 'login', null, null, null, null, null,
            new \DateTimeImmutable(),
        );

        $this->assertNull($activity->getCollection());
        $this->assertNull($activity->getItem());
        $this->assertNull($activity->getUserId());
        $this->assertNull($activity->getIp());
        $this->assertNull($activity->getUserAgent());
    }
}
