<?php

/**
 * @file CollectionMetaSetSortFieldTest.php
 *
 * Tests for CollectionMeta::setSortField().
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
#[CoversMethod(CollectionMeta::class, 'setSortField')]
final class CollectionMetaSetSortFieldTest extends CollectionMetaTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testSetSortFieldPersistsValue(): void
    {
        $this->class->setSortField('sort');

        $this->assertSame('sort', $this->class->getSortField());
    }

    public function testSetSortFieldReturnsStatic(): void
    {
        $result = $this->class->setSortField('sort');

        $this->assertSame($this->class, $result);
    }

    public function testSetSortFieldUpdatesUpdatedAt(): void
    {
        $this->assertNull($this->class->getUpdatedAt());

        $this->class->setSortField('sort');

        $this->assertNotNull($this->class->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testSetSortFieldAcceptsNull(): void
    {
        $this->class->setSortField('sort');
        $this->class->setSortField(null);

        $this->assertNull($this->class->getSortField());
    }
}
