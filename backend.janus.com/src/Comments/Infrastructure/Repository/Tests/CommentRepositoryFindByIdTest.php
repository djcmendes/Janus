<?php

/**
 * @file CommentRepositoryFindByIdTest.php
 *
 * Tests for CommentRepository::findById().
 *
 * @package App\Comments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Repository\Tests;

use App\Comments\Domain\Entity\Comment;
use App\Comments\Infrastructure\Persistence\Doctrine\Entity\CommentEntity;
use App\Comments\Infrastructure\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentRepository::findById().
 *
 * Covers: domain Comment returned when the record exists (mapped from CommentEntity),
 * null returned when no match, and the correct UUID forwarded to the entity manager.
 */
#[CoversClass(className: CommentRepository::class)]
#[CoversMethod(CommentRepository::class, 'findById')]
final class CommentRepositoryFindByIdTest extends CommentRepositoryTest
{
    /** @var string */
    private const string LOOKUP_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that findById() returns a domain Comment when the record exists.
     */
    public function testFindByIdReturnsDomainCommentForExistingId(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeCommentEntity());

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertInstanceOf(Comment::class, $result);
    }

    /**
     * Test that findById() maps the CommentEntity fields onto the returned domain Comment.
     */
    public function testFindByIdMapsEntityToDomainComment(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn($this->makeCommentEntity('articles', '7'));

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertSame('articles', $result->getCollection());
        $this->assertSame('7', $result->getItem());
    }

    /**
     * Test that findById() returns null when no record exists for the given UUID.
     */
    public function testFindByIdReturnsNullForNonExistentId(): void
    {
        $this->entityManager
            ->method('find')
            ->willReturn(null);

        $result = $this->class->findById(self::LOOKUP_UUID);

        $this->assertNull($result);
    }

    /**
     * Test that findById() passes CommentEntity::class and the UUID to the entity manager.
     */
    public function testFindByIdPassesCorrectClassAndIdToEntityManager(): void
    {
        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(CommentEntity::class, self::LOOKUP_UUID)
            ->willReturn(null);

        $this->class->findById(self::LOOKUP_UUID);
    }
}
