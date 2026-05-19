<?php

/**
 * @file CollectionMetaEntitySetLabelTest.php
 *
 * Tests for CollectionMetaEntity::setLabel().
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
#[CoversMethod(CollectionMetaEntity::class, 'setLabel')]
final class CollectionMetaEntitySetLabelTest extends CollectionMetaEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetLabelPersistsValue(): void
    {
        $this->class->setLabel('Articles');

        $this->assertSame('Articles', $this->class->getLabel());
    }

    public function testSetLabelReturnsStatic(): void
    {
        $result = $this->class->setLabel('Articles');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetLabelAcceptsNull(): void
    {
        $this->class->setLabel('Articles');
        $this->class->setLabel(null);

        $this->assertNull($this->class->getLabel());
    }
}
