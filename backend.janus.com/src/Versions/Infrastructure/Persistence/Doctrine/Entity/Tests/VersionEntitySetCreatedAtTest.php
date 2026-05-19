<?php

/**
 * @file VersionEntitySetCreatedAtTest.php
 *
 * Tests for VersionEntity::setCreatedAt().
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Versions\Infrastructure\Persistence\Doctrine\Entity\VersionEntity;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that setCreatedAt() stores the timestamp and returns static.
 */
#[CoversClass(className: VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setCreatedAt')]
final class VersionEntitySetCreatedAtTest extends VersionEntityTest
{
    public function testSetCreatedAtStoresValue(): void
    {
        $ts = new DateTimeImmutable('2025-03-01T08:00:00+00:00');
        $this->class->setCreatedAt($ts);

        $this->assertSame($ts->getTimestamp(), $this->class->getCreatedAt()->getTimestamp());
    }

    public function testSetCreatedAtReturnsStatic(): void
    {
        $result = $this->class->setCreatedAt(new DateTimeImmutable());
        $this->assertSame($this->class, $result);
    }
}
