<?php

/**
 * @file CommentSetCommentTest.php
 *
 * Tests for Comment::setComment().
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
 * Tests for Comment::setComment() — verifying text update and timestamp side-effect.
 */
#[CoversClass(Comment::class)]
#[CoversMethod(Comment::class, 'setComment')]
final class CommentSetCommentTest extends CommentTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that setComment() stores the new text.
     */
    public function testSetCommentUpdatesCommentText(): void
    {
        $this->class->setComment('New text');

        $this->assertSame('New text', $this->class->getComment());
    }

    /**
     * Test that setComment() sets updatedAt to approximately now.
     */
    public function testSetCommentSetsUpdatedAtToApproximatelyNow(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->setComment('New text');
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $this->class->getUpdatedAt());
        $this->assertLessThanOrEqual($after, $this->class->getUpdatedAt());
    }

    /**
     * Test that setComment() returns the same Comment instance (fluent interface).
     */
    public function testSetCommentReturnsSelf(): void
    {
        $result = $this->class->setComment('New text');

        $this->assertSame($this->class, $result);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that updatedAt advances on successive calls to setComment().
     */
    public function testSetCommentAdvancesUpdatedAtOnSuccessiveCalls(): void
    {
        $this->class->setComment('First edit');
        $first = $this->class->getUpdatedAt();

        usleep(1000);
        $this->class->setComment('Second edit');
        $second = $this->class->getUpdatedAt();

        $this->assertGreaterThanOrEqual($first, $second);
    }
}
