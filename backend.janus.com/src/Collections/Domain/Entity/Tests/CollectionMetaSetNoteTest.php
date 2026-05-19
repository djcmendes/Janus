<?php

/**
 * @file CollectionMetaSetNoteTest.php
 *
 * Tests for CollectionMeta::setNote().
 *
 * @package App\Collections\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity\Tests;

use App\Collections\Domain\Entity\CollectionMeta;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMeta::class)]
#[CoversMethod(CollectionMeta::class, 'setNote')]
final class CollectionMetaSetNoteTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetNotePersistsValue(): void
    {
        $this->class->setNote('Main blog articles collection.');

        $this->assertSame('Main blog articles collection.', $this->class->getNote());
    }

    public function testSetNoteReturnsStatic(): void
    {
        $result = $this->class->setNote('Main blog articles collection.');

        $this->assertSame($this->class, $result);
    }

    public function testSetNoteUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setNote('Main blog articles collection.');

        $this->assertNotNull($this->class->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetNoteAcceptsNull(): void
    {
        $this->class->setNote('Some note.');
        $this->class->setNote(null);

        $this->assertNull($this->class->getNote());
    }
}
