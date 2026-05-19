<?php

/**
 * @file CommentReconstituteTest.php
 *
 * Tests for Comment::reconstitute().
 *
 * @package App\Comments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Entity\Tests;

use App\Comments\Domain\Entity\Comment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for Comment::reconstitute() — hydrating an entity from persisted state.
 */
#[CoversClass(className: Comment::class)]
#[CoversMethod(Comment::class, 'reconstitute')]
final class CommentReconstituteTest extends CommentTest
{
    /**
     * UUID used as the lookup identifier in all get() test scenarios.
     * @var string
     */
    private const string FIXED_UUID = 'aaaaaaaa-0000-7000-8000-000000000001';

    /**
     * Test that reconstitute() uses the provided id instead of generating one.
     */
    public function testReconstituteUsesSuppliedId(): void
    {
        $comment = Comment::reconstitute(
            self::FIXED_UUID,
            'posts', '42', 'Hello', 'user-uuid',
            new \DateTimeImmutable(),
            null,
        );

        $this->assertSame(self::FIXED_UUID, $comment->getId());
    }

    /**
     * Test that reconstitute() uses the provided createdAt instead of generating one.
     */
    public function testReconstituteUsesSuppliedCreatedAt(): void
    {
        $ts      = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $comment = Comment::reconstitute(
            self::FIXED_UUID,
            'posts', '42', 'Hello', 'user-uuid',
            $ts,
            null,
        );

        $this->assertSame($ts, $comment->getCreatedAt());
    }

    /**
     * Test that reconstitute() restores all fields when all arguments are provided.
     */
    public function testReconstitutePopulatesAllFields(): void
    {
        $createdAt = new \DateTimeImmutable('2020-01-01T00:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2020-06-01T00:00:00+00:00');

        $comment = Comment::reconstitute(
            id:         self::FIXED_UUID,
            collection: 'articles',
            item:       '99',
            comment:    'Updated text',
            userId:     'bbbbbbbb-0000-7000-8000-000000000002',
            createdAt:  $createdAt,
            updatedAt:  $updatedAt,
        );

        $this->assertSame('articles', $comment->getCollection());
        $this->assertSame('99', $comment->getItem());
        $this->assertSame('Updated text', $comment->getComment());
        $this->assertSame('bbbbbbbb-0000-7000-8000-000000000002', $comment->getUserId());
        $this->assertSame($updatedAt, $comment->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that reconstitute() accepts null for updatedAt.
     */
    public function testReconstituteAcceptsNullUpdatedAt(): void
    {
        $comment = Comment::reconstitute(
            self::FIXED_UUID,
            'posts', '1', 'text', 'user-uuid',
            new \DateTimeImmutable(),
            null,
        );

        $this->assertNull($comment->getUpdatedAt());
    }
}
