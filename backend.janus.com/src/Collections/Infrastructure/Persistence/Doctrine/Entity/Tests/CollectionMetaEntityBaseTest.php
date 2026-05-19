<?php

/**
 * @file CollectionMetaEntityBaseTest.php
 *
 * Constructor and default-value tests for CollectionMetaEntity.
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Collections\Infrastructure\Persistence\Doctrine\Entity\CollectionMetaEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(className: CollectionMetaEntity::class)]
final class CollectionMetaEntityBaseTest extends CollectionMetaEntityTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testIsInstanceOfCollectionMetaEntity(): void
    {
        $this->assertInstanceOf(CollectionMetaEntity::class, $this->class);
    }

    public function testDefaultIdIsNull(): void
    {
        $this->assertNull($this->class->getId());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testDefaultLabelIsNull(): void
    {
        $this->assertNull($this->class->getLabel());
    }

    public function testDefaultIconIsNull(): void
    {
        $this->assertNull($this->class->getIcon());
    }

    public function testDefaultNoteIsNull(): void
    {
        $this->assertNull($this->class->getNote());
    }

    public function testDefaultHiddenIsFalse(): void
    {
        $this->assertFalse($this->class->isHidden());
    }

    public function testDefaultSingletonIsFalse(): void
    {
        $this->assertFalse($this->class->isSingleton());
    }

    public function testDefaultSortFieldIsNull(): void
    {
        $this->assertNull($this->class->getSortField());
    }

    public function testDefaultUpdatedAtIsNull(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }
}
