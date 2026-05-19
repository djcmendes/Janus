<?php

/**
 * @file CollectionMetaEntitySetCreatedAtTest.php
 *
 * Tests for CollectionMetaEntity::setCreatedAt().
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
#[CoversMethod(CollectionMetaEntity::class, 'setCreatedAt')]
final class CollectionMetaEntitySetCreatedAtTest extends CollectionMetaEntityTest
{
    public function testSetCreatedAtPersistsValue(): void
    {
        $ts = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        $this->class->setCreatedAt($ts);
        $this->assertSame($ts, $this->class->getCreatedAt());
    }

    public function testSetCreatedAtReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setCreatedAt(new \DateTimeImmutable()));
    }
}
