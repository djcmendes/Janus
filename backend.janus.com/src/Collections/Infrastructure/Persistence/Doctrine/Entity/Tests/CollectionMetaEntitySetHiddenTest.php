<?php

/**
 * @file CollectionMetaEntitySetHiddenTest.php
 *
 * Tests for CollectionMetaEntity::setHidden().
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
#[CoversMethod(CollectionMetaEntity::class, 'setHidden')]
final class CollectionMetaEntitySetHiddenTest extends CollectionMetaEntityTest
{
    public function testSetHiddenToTrue(): void
    {
        $this->class->setHidden(true);
        $this->assertTrue($this->class->isHidden());
    }

    public function testSetHiddenToFalse(): void
    {
        $this->class->setHidden(true);
        $this->class->setHidden(false);
        $this->assertFalse($this->class->isHidden());
    }

    public function testSetHiddenReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setHidden(true));
    }
}
