<?php

/**
 * @file CollectionMetaSetHiddenTest.php
 *
 * Tests for CollectionMeta::setHidden().
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
#[CoversMethod(CollectionMeta::class, 'setHidden')]
final class CollectionMetaSetHiddenTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

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
        $result = $this->class->setHidden(true);

        $this->assertSame($this->class, $result);
    }

    public function testSetHiddenUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setHidden(true);

        $this->assertNotNull($this->class->getUpdatedAt());
    }
}
