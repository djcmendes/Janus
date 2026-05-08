<?php

/**
 * @file CollectionMetaEntitySetIconTest.php
 *
 * Tests for CollectionMetaEntity::setIcon().
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
#[CoversMethod(CollectionMetaEntity::class, 'setIcon')]
final class CollectionMetaEntitySetIconTest extends CollectionMetaEntityTest
{
    public function testSetIconPersistsValue(): void
    {
        $this->class->setIcon('mdi-file-document');
        $this->assertSame('mdi-file-document', $this->class->getIcon());
    }

    public function testSetIconReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setIcon('mdi-file-document'));
    }

    public function testSetIconAcceptsNull(): void
    {
        $this->class->setIcon('mdi-file-document');
        $this->class->setIcon(null);
        $this->assertNull($this->class->getIcon());
    }
}
