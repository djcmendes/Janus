<?php

/**
 * @file CollectionMetaRepositoryCountTest.php
 *
 * Tests for CollectionMetaRepository::count().
 *
 * @package App\Collections\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Repository\Tests;

use App\Collections\Infrastructure\Repository\CollectionMetaRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

#[CoversClass(className: CollectionMetaRepository::class)]
#[CoversMethod(CollectionMetaRepository::class, 'count')]
final class CollectionMetaRepositoryCountTest extends CollectionMetaRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    public function testCountDelegatesToParentAndReturnsInt(): void
    {
        $this->persister->method('count')->willReturn(7);

        $result = $this->class->count([]);

        $this->assertSame(7, $result);
    }

    public function testCountWithNoCriteriaUsesEmptyArray(): void
    {
        $this->persister->method('count')->willReturn(3);

        $result = $this->class->count();

        $this->assertSame(3, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    public function testCountReturnsZeroWhenNoRecordsExist(): void
    {
        $this->persister->method('count')->willReturn(0);

        $result = $this->class->count();

        $this->assertSame(0, $result);
    }
}
