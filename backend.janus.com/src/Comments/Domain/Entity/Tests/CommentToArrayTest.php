<?php

/**
 * @file CommentToArrayTest.php
 *
 * Tests for Comment::toArray().
 *
 * @package App\Comments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Entity\Tests;

use App\Comments\Domain\Entity\Comment;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for Comment::toArray() — verifying the serialised representation.
 */
#[CoversClass(Comment::class)]
#[CoversMethod(Comment::class, 'toArray')]
final class CommentToArrayTest extends CommentTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that toArray() includes all expected keys.
     */
    public function testToArrayContainsAllExpectedKeys(): void
    {
        $array = $this->class->toArray();

        foreach (['id', 'collection', 'item', 'comment', 'user', 'created_at', 'updated_at'] as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * Test that toArray() includes the entity UUID under the 'id' key.
     */
    public function testToArrayIncludesIdAsString(): void
    {
        $array = $this->class->toArray();

        $this->assertIsString($array['id']);
        $this->assertSame($this->class->getId(), $array['id']);
    }

    /**
     * Test that toArray() formats createdAt as an ATOM-format string.
     */
    public function testToArrayFormatsCreatedAtAsAtomString(): void
    {
        $array = $this->class->toArray();

        $this->assertSame(
            $this->class->getCreatedAt()->format(DateTimeInterface::ATOM),
            $array['created_at'],
        );
    }

    /**
     * Test that toArray() maps userId to the 'user' key.
     */
    public function testToArrayMapsUserIdToUserKey(): void
    {
        $array = $this->class->toArray();

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $array['user']);
        $this->assertArrayNotHasKey('userId', $array);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that toArray() returns null for updated_at when the comment has never been edited.
     */
    public function testToArrayReturnsNullUpdatedAtWhenNeverEdited(): void
    {
        $this->assertNull($this->class->toArray()['updated_at']);
    }

    /**
     * Test that toArray() formats updatedAt as an ATOM string after an edit.
     */
    public function testToArrayFormatsUpdatedAtAfterEdit(): void
    {
        $this->class->setComment('Edited');
        $array = $this->class->toArray();

        $this->assertNotNull($array['updated_at']);
        $this->assertSame(
            $this->class->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $array['updated_at'],
        );
    }
}
