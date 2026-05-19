<?php

/**
 * @file CollectionMetaEntitySetUpdatedAtTest.php
 *
 * Tests for CollectionMetaEntity::setUpdatedAt().
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaEntity::class)]
#[CoversMethod(CollectionMetaEntity::class, 'setUpdatedAt')]
final class CollectionMetaEntitySetUpdatedAtTest extends CollectionMetaEntityTest
{
    public function testSetUpdatedAtPersistsValue(): void
    {
        $ts = new \DateTimeImmutable('2024-06-01T12:00:00+00:00');
        $this->class->setUpdatedAt($ts);
        $this->assertSame($ts, $this->class->getUpdatedAt());
    }

    public function testSetUpdatedAtReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setUpdatedAt(new \DateTimeImmutable()));
    }

    public function testSetUpdatedAtAcceptsNull(): void
    {
        $this->class->setUpdatedAt(new \DateTimeImmutable());
        $this->class->setUpdatedAt(null);
        $this->assertNull($this->class->getUpdatedAt());
    }
}
