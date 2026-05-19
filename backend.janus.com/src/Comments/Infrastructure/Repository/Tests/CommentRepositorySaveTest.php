<?php

/**
 * @file CommentRepositorySaveTest.php
 *
 * Tests for CommentRepository::save().
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
 * Tests for CommentRepository::save().
 *
 * Covers: entity persistence via CommentMapper, entity manager flush, and
 * the correct CommentEntity type forwarded to the entity manager.
 */
#[CoversClass(className: CommentRepository::class)]
#[CoversMethod(CommentRepository::class, 'save')]
final class CommentRepositorySaveTest extends CommentRepositoryTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that save() calls persist() on the entity manager with a CommentEntity.
     */
    public function testSavePersistsCommentEntityToEntityManager(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(CommentEntity::class));

        $this->entityManager->method('flush');

        $this->class->save($this->makeComment());
    }

    /**
     * Test that save() calls flush() on the entity manager when flush is true.
     */
    public function testSaveFlushesEntityManagerWhenFlushIsTrue(): void
    {
        $this->entityManager->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->class->save($this->makeComment(), true);
    }

    /**
     * Test that save() persists and flushes in a single call.
     */
    public function testSavePersistsAndFlushesInSingleCall(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(CommentEntity::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->class->save($this->makeComment('articles', '5', 'Test comment'));
    }

    /**
     * Test that save() maps the domain collection onto the persisted CommentEntity.
     */
    public function testSavePersistsEntityWithCorrectCollection(): void
    {
        $captured = null;
        $this->entityManager
            ->method('persist')
            ->with($this->callback(static function (CommentEntity $e) use (&$captured): bool {
                $captured = $e;
                return true;
            }));

        $this->entityManager->method('flush');

        $this->class->save($this->makeComment('articles', '5', 'My comment'));

        $this->assertSame('articles', $captured->getCollection());
        $this->assertSame('5', $captured->getItem());
    }

    /**
     * Test that save() returns void and produces no output.
     */
    public function testSaveReturnsVoid(): void
    {
        $this->entityManager->method('persist');
        $this->entityManager->method('flush');

        $result = $this->class->save($this->makeComment());

        $this->assertNull($result);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that save() does not flush when $flush is false.
     */
    public function testSaveDoesNotFlushWhenFlushIsFalse(): void
    {
        $this->entityManager->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->class->save($this->makeComment(), false);
    }
}
