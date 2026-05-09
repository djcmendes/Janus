<?php

/**
 * @file CommentIsOwnedByTest.php
 *
 * Tests for Comment::isOwnedBy().
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
 * Tests for Comment::isOwnedBy() — verifying ownership checks.
 */
#[CoversClass(Comment::class)]
#[CoversMethod(Comment::class, 'isOwnedBy')]
final class CommentIsOwnedByTest extends CommentTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that isOwnedBy() returns true when the given userId matches the author.
     */
    public function testIsOwnedByReturnsTrueForAuthor(): void
    {
        $this->assertTrue($this->class->isOwnedBy('aaaaaaaa-0000-7000-8000-000000000001'));
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that isOwnedBy() returns false when the given userId does not match the author.
     */
    public function testIsOwnedByReturnsFalseForDifferentUser(): void
    {
        $this->assertFalse($this->class->isOwnedBy('bbbbbbbb-0000-7000-8000-000000000002'));
    }

    /**
     * Test that isOwnedBy() performs an exact string comparison.
     */
    public function testIsOwnedByPerformsExactStringComparison(): void
    {
        $this->assertFalse($this->class->isOwnedBy('AAAAAAAA-0000-7000-8000-000000000001'));
    }
}
