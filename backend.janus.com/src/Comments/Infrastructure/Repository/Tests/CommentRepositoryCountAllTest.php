<?php

/**
 * @file CommentRepositoryCountAllTest.php
 *
 * Tests for CommentRepository::countAll().
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Infrastructure\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentRepository::countAll().
 *
 * Covers: correct integer returned, zero count, filter WHERE clauses,
 * and no WHERE clause when no filters are provided.
 */
#[CoversClass(CommentRepository::class)]
#[CoversMethod(CommentRepository::class, 'countAll')]
final class CommentRepositoryCountAllTest extends CommentRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that countAll() returns the integer result from the scalar query.
     */
    public function testCountAllReturnsIntegerResult(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('42');

        $result = $this->class->countAll();

        $this->assertSame(42, $result);
    }

    /**
     * Test that countAll() returns zero when no records match.
     */
    public function testCountAllReturnsZeroWhenNoRecords(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('0');

        $result = $this->class->countAll();

        $this->assertSame(0, $result);
    }

    /**
     * Test that countAll() casts the string scalar result to int.
     */
    public function testCountAllCastsScalarResultToInt(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('10');

        $this->assertIsInt($this->class->countAll());
    }

    /**
     * Test that countAll() adds a collection WHERE clause when collection is provided.
     */
    public function testCountAllAddsCollectionFilterWhenProvided(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('c.collection = :collection')
            ->willReturn($this->queryBuilder);

        $this->query->method('getSingleScalarResult')->willReturn('5');

        $this->class->countAll('posts');
    }

    /**
     * Test that countAll() adds an item WHERE clause when item is provided.
     */
    public function testCountAllAddsItemFilterWhenProvided(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('c.item = :item')
            ->willReturn($this->queryBuilder);

        $this->query->method('getSingleScalarResult')->willReturn('3');

        $this->class->countAll(null, '42');
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that countAll() adds no WHERE clause when no filters are provided.
     */
    public function testCountAllAddsNoWhereClauseWithoutFilters(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('andWhere');

        $this->query->method('getSingleScalarResult')->willReturn('0');

        $this->class->countAll();
    }
}
