<?php

/**
 * @file VersionEntitySetUpdatedAtTest.php
 *
 * Tests for VersionEntity::setUpdatedAt().
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
 * Verifies that setUpdatedAt() stores the timestamp, handles null, and returns static.
 */
#[CoversClass(VersionEntity::class)]
#[CoversMethod(VersionEntity::class, 'setUpdatedAt')]
final class VersionEntitySetUpdatedAtTest extends VersionEntityTest
{
    public function testSetUpdatedAtStoresValue(): void
    {
        $ts = new DateTimeImmutable('2025-06-01T10:00:00+00:00');
        $this->class->setUpdatedAt($ts);

        $this->assertSame($ts->getTimestamp(), $this->class->getUpdatedAt()->getTimestamp());
    }

    public function testSetUpdatedAtWithNullClearsValue(): void
    {
        $this->class->setUpdatedAt(new DateTimeImmutable());
        $this->class->setUpdatedAt(null);

        $this->assertNull($this->class->getUpdatedAt());
    }

    public function testSetUpdatedAtReturnsStatic(): void
    {
        $result = $this->class->setUpdatedAt(null);
        $this->assertSame($this->class, $result);
    }
}
