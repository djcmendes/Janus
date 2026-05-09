<?php

/**
 * @file CommentRepositoryDeleteTest.php
 *
 * Tests for CommentRepository::delete().
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentRepository::delete().
 *
 * Covers: locating the managed entity by id, calling remove() and flush(),
 * and skipping removal when no entity is found.
 */
#[CoversClass(CommentRepository::class)]
#[CoversMethod(CommentRepository::class, 'delete')]
final class CommentRepositoryDeleteTest extends CommentRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that delete() calls remove() on the entity manager with a CommentEntity.
     */
    public function testDeleteCallsRemoveWithCommentEntity(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeCommentEntity());

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf(CommentEntity::class));

        $this->entityManager->method('flush');

        $this->class->delete($this->makeComment());
    }

    /**
     * Test that delete() calls flush() after removing the entity.
     */
    public function testDeleteFlushesEntityManager(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeCommentEntity());

        $this->entityManager->method('remove');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->class->delete($this->makeComment());
    }

    /**
     * Test that delete() returns void and produces no output.
     */
    public function testDeleteReturnsVoid(): void
    {
        $this->entityManager->method('find')->willReturn($this->makeCommentEntity());
        $this->entityManager->method('remove');
        $this->entityManager->method('flush');

        $result = $this->class->delete($this->makeComment());

        $this->assertNull($result);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that delete() does not call remove() when the entity is not found.
     */
    public function testDeleteDoesNotRemoveWhenEntityNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $this->entityManager
            ->expects($this->never())
            ->method('remove');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->class->delete($this->makeComment());
    }
}
