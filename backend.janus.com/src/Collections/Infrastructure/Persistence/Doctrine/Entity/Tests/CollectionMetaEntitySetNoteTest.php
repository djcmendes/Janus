<?php

/**
 * @file CollectionMetaEntitySetNoteTest.php
 *
 * Tests for CollectionMetaEntity::setNote().
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
#[CoversMethod(CollectionMetaEntity::class, 'setNote')]
final class CollectionMetaEntitySetNoteTest extends CollectionMetaEntityTest
{
    public function testSetNotePersistsValue(): void
    {
        $this->class->setNote('Main blog articles collection.');
        $this->assertSame('Main blog articles collection.', $this->class->getNote());
    }

    public function testSetNoteReturnsStatic(): void
    {
        $this->assertSame($this->class, $this->class->setNote('Main blog articles collection.'));
    }

    public function testSetNoteAcceptsNull(): void
    {
        $this->class->setNote('Some note.');
        $this->class->setNote(null);
        $this->assertNull($this->class->getNote());
    }
}
