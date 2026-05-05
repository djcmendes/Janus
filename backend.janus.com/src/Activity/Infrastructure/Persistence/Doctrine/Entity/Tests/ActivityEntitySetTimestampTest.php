<?php

/**
 * @file ActivityEntitySetTimestampTest.php
 *
 * Tests for ActivityEntity::setTimestamp() and ActivityEntity::getTimestamp().
 *
 * @package App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Activity\Infrastructure\Persistence\Doctrine\Entity\ActivityEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(ActivityEntity::class)]
#[CoversMethod(ActivityEntity::class, 'setTimestamp')]
final class ActivityEntitySetTimestampTest extends ActivityEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetTimestampStoresValue(): void
    {
        $ts = new \DateTimeImmutable('2024-06-15T12:00:00+00:00');
        $this->class->setTimestamp($ts);

        $this->assertSame($ts, $this->class->getTimestamp());
    }

    public function testSetTimestampReturnsStaticInstance(): void
    {
        $result = $this->class->setTimestamp(new \DateTimeImmutable());

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetTimestampOverwritesPreviousValue(): void
    {
        $first  = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $second = new \DateTimeImmutable('2024-06-01T00:00:00+00:00');

        $this->class->setTimestamp($first);
        $this->class->setTimestamp($second);

        $this->assertSame($second, $this->class->getTimestamp());
    }
}
