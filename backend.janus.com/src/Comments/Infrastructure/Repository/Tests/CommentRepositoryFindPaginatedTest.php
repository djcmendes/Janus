<?php

/**
 * @file CommentRepositoryFindPaginatedTest.php
 *
 * Tests for CommentRepository::findPaginated().
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentRepository::findPaginated().
 *
 * Covers: domain Comment array returned (mapped from CommentEntity), empty result,
 * limit/offset forwarded, and each filter applied as a WHERE clause.
 */
#[CoversClass(CommentRepository::class)]
#[CoversMethod(CommentRepository::class, 'findPaginated')]
final class CommentRepositoryFindPaginatedTest extends CommentRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that findPaginated() returns an array of domain Comments.
     */
    public function testFindPaginatedReturnsDomainCommentArray(): void
    {
        $this->query->method('getResult')->willReturn([$this->makeCommentEntity()]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Comment::class, $result[0]);
    }

    /**
     * Test that findPaginated() returns an empty array when no records match.
     */
    public function testFindPaginatedReturnsEmptyArrayWhenNoResults(): void
    {
        $this->query->method('getResult')->willReturn([]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertSame([], $result);
    }

    /**
     * Test that findPaginated() forwards the limit to setMaxResults().
     */
    public function testFindPaginatedForwardsLimitToSetMaxResults(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setMaxResults')
            ->with(25)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(25, 0);
    }

    /**
     * Test that findPaginated() forwards the offset to setFirstResult().
     */
    public function testFindPaginatedForwardsOffsetToSetFirstResult(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('setFirstResult')
            ->with(50)
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 50);
    }

    /**
     * Test that findPaginated() adds a collection WHERE clause when collection is provided.
     */
    public function testFindPaginatedAddsCollectionWhereClauseWhenProvided(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('c.collection = :collection')
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, 'posts');
    }

    /**
     * Test that findPaginated() adds an item WHERE clause when item is provided.
     */
    public function testFindPaginatedAddsItemWhereClauseWhenProvided(): void
    {
        $this->queryBuilder
            ->expects($this->once())
            ->method('andWhere')
            ->with('c.item = :item')
            ->willReturn($this->queryBuilder);

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0, null, '42');
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that findPaginated() adds no WHERE clause when no filters are provided.
     */
    public function testFindPaginatedAddsNoWhereClauseWithoutFilters(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('andWhere');

        $this->query->method('getResult')->willReturn([]);

        $this->class->findPaginated(10, 0);
    }

    /**
     * Test that findPaginated() maps multiple returned entities to domain Comments.
     */
    public function testFindPaginatedMapsMultipleEntitiesToDomainComments(): void
    {
        $this->query->method('getResult')->willReturn([
            $this->makeCommentEntity('posts', '1'),
            $this->makeCommentEntity('posts', '2'),
        ]);

        $result = $this->class->findPaginated(10, 0);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Comment::class, $result);
    }
}
