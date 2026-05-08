<?php

/**
 * @file CollectionMetaSetIconTest.php
 *
 * Tests for CollectionMeta::setIcon().
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(CollectionMeta::class)]
#[CoversMethod(CollectionMeta::class, 'setIcon')]
final class CollectionMetaSetIconTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetIconPersistsValue(): void
    {
        $this->class->setIcon('mdi-file-document');

        $this->assertSame('mdi-file-document', $this->class->getIcon());
    }

    public function testSetIconReturnsStatic(): void
    {
        $result = $this->class->setIcon('mdi-file-document');

        $this->assertSame($this->class, $result);
    }

    public function testSetIconUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setIcon('mdi-file-document');

        $this->assertNotNull($this->class->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetIconAcceptsNull(): void
    {
        $this->class->setIcon('mdi-file-document');
        $this->class->setIcon(null);

        $this->assertNull($this->class->getIcon());
    }
}
