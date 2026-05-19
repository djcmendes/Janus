<?php

/**
 * @file CommentBaseTest.php
 *
 * Constructor and interface compliance tests for the Comment domain entity.
 *
 * @package App\Comments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Entity\Tests;

use App\Comments\Domain\Entity\Comment;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Constructor and interface compliance tests for the Comment domain entity.
 */
#[CoversClass(className: Comment::class)]
final class CommentBaseTest extends CommentTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of Comment.
     */
    public function testIsInstanceOfComment(): void
    {
        $this->assertInstanceOf(Comment::class, $this->class);
    }

    /**
     * Test that the constructor stores the collection argument.
     */
    public function testConstructorSetsCollection(): void
    {
        $this->assertSame('posts', $this->class->getCollection());
    }

    /**
     * Test that the constructor stores the item argument.
     */
    public function testConstructorSetsItem(): void
    {
        $this->assertSame('42', $this->class->getItem());
    }

    /**
     * Test that the constructor stores the comment argument.
     */
    public function testConstructorSetsComment(): void
    {
        $this->assertSame('Hello world', $this->class->getComment());
    }

    /**
     * Test that the constructor stores the userId argument.
     */
    public function testConstructorSetsUserId(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->getUserId());
    }

    /**
     * Test that the constructor generates a valid UUIDv7 string.
     */
    public function testConstructorGeneratesUuidV7String(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $this->class->getId(),
        );
    }

    /**
     * Test that the constructor sets createdAt to approximately the current time.
     */
    public function testConstructorSetsCreatedAtToApproximatelyNow(): void
    {
        $before  = new \DateTimeImmutable();
        $comment = new Comment('c', 'i', 't', 'u');
        $after   = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $comment->getCreatedAt());
        $this->assertLessThanOrEqual($after, $comment->getCreatedAt());
    }

    /**
     * Test that updatedAt is null immediately after construction.
     */
    public function testConstructorLeavesUpdatedAtNull(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that each Comment instance receives a unique UUID.
     */
    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new Comment('c', 'i', 't', 'u');
        $b = new Comment('c', 'i', 't', 'u');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
