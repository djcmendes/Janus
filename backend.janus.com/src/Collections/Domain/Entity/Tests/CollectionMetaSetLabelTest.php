<?php

/**
 * @file CollectionMetaSetLabelTest.php
 *
 * Tests for CollectionMeta::setLabel().
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
#[CoversMethod(CollectionMeta::class, 'setLabel')]
final class CollectionMetaSetLabelTest extends CollectionMetaTest
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

    public function testSetLabelUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setLabel('Articles');

        $this->assertNotNull($this->class->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetLabelAcceptsNull(): void
    {
        $this->class->setLabel('Articles');
        $this->class->setLabel(null);

        $this->assertNull($this->class->getLabel());
    }
}
