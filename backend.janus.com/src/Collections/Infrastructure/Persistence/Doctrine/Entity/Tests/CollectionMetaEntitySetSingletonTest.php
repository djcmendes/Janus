<?php

/**
 * @file CollectionMetaEntitySetSingletonTest.php
 *
 * Tests for CollectionMetaEntity::setSingleton().
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
#[CoversMethod(CollectionMetaEntity::class, 'setSingleton')]
final class CollectionMetaEntitySetSingletonTest extends CollectionMetaEntityTest
{
    public function testSetSingletonToTrue(): void
    {
        $this->class->setSingleton(true);
        $this->assertTrue($this->class->isSingleton());
    }

    public function testSetSingletonToFalse(): void
    {
        $this->class->setSingleton(true);
        $this->class->setSingleton(false);
        $this->assertFalse($this->class->isSingleton());
    }

    public function testSetSingletonReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setSingleton(true));
    }
}
