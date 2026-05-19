<?php

/**
 * @file VersionRepositoryCountAllTest.php
 *
 * Tests for VersionRepository::countAll().
 *
 * @package App\Versions\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Repository\Tests;

use App\Versions\Infrastructure\Repository\VersionRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies countAll() returns the correct integer count from the repository.
 */
#[CoversClass(className: VersionRepository::class)]
#[CoversMethod(VersionRepository::class, 'countAll')]
final class VersionRepositoryCountAllTest extends VersionRepositoryTest
{
    /**
     * Test that countAll() returns the integer from getSingleScalarResult().
     */
    public function testCountAllReturnsCorrectInteger(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('42');

        $result = $this->class->countAll();

        $this->assertSame(42, $result);
    }

    /**
     * Test that countAll() returns zero when there are no records.
     */
    public function testCountAllReturnsZeroForEmptyRepository(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('0');

        $result = $this->class->countAll();

        $this->assertSame(0, $result);
    }

    /**
     * Test that countAll() does not add a WHERE clause when no filters are set.
     */
    public function testCountAllAddsNoWhereWithoutFilters(): void
    {
        $this->queryBuilder->expects($this->never())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn('0');

        $this->class->countAll(null, null);
    }

    /**
     * Test that countAll() adds a collection filter when provided.
     */
    public function testCountAllAddsCollectionFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn('5');

        $this->class->countAll('articles', null);
    }

    /**
     * Test that countAll() adds an item filter when provided.
     */
    public function testCountAllAddsItemFilter(): void
    {
        $this->queryBuilder->expects($this->atLeastOnce())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn('3');

        $this->class->countAll(null, 'item-uuid-1');
    }
}
