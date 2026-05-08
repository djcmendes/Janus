<?php

/**
 * @file CollectionMetaEntitySetSortFieldTest.php
 *
 * Tests for CollectionMetaEntity::setSortField().
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CollectionMetaEntity::class)]
#[CoversMethod(CollectionMetaEntity::class, 'setSortField')]
final class CollectionMetaEntitySetSortFieldTest extends CollectionMetaEntityTest
{
    public function testSetSortFieldPersistsValue(): void
    {
        $this->class->setSortField('sort');
        $this->assertSame('sort', $this->class->getSortField());
    }

    public function testSetSortFieldReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setSortField('sort'));
    }

    public function testSetSortFieldAcceptsNull(): void
    {
        $this->class->setSortField('sort');
        $this->class->setSortField(null);
        $this->assertNull($this->class->getSortField());
    }
}
