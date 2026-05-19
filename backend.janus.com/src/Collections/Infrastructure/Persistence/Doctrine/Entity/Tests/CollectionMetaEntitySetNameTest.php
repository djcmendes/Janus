<?php

/**
 * @file CollectionMetaEntitySetNameTest.php
 *
 * Tests for CollectionMetaEntity::setName().
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
#[CoversMethod(CollectionMetaEntity::class, 'setName')]
final class CollectionMetaEntitySetNameTest extends CollectionMetaEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetNamePersistsValue(): void
    {
        $this->class->setName('articles');

        $this->assertSame('articles', $this->class->getName());
    }

    public function testSetNameReturnsStatic(): void
    {
        $result = $this->class->setName('articles');

        $this->assertSame($this->class, $result);
    }
}
