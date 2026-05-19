<?php

/**
 * @file CommentDtoBaseTest.php
 *
 * Constructor and interface compliance tests for CommentDto.
 *
 * @package App\Comments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\DTO\Tests;

use App\Comments\Application\DTO\CommentDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that CommentDto is correctly instantiated and exposes all expected properties.
 */
#[CoversClass(className: CommentDto::class)]
final class CommentDtoBaseTest extends CommentDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that the SUT is an instance of CommentDto.
     */
    public function testIsInstanceOfCommentDto(): void
    {
        $this->assertInstanceOf(CommentDto::class, $this->class);
    }

    /**
     * Test that the id property is a non-empty string.
     */
    public function testIdIsNonEmptyString(): void
    {
        $this->assertIsString($this->class->id);
        $this->assertNotEmpty($this->class->id);
    }

    /**
     * Test that the collection property matches the source entity.
     */
    public function testCollectionMatchesSourceEntity(): void
    {
        $this->assertSame('posts', $this->class->collection);
    }

    /**
     * Test that the item property matches the source entity.
     */
    public function testItemMatchesSourceEntity(): void
    {
        $this->assertSame('42', $this->class->item);
    }

    /**
     * Test that the comment property matches the source entity.
     */
    public function testCommentMatchesSourceEntity(): void
    {
        $this->assertSame('Hello world', $this->class->comment);
    }

    /**
     * Test that the userId property matches the source entity.
     */
    public function testUserIdMatchesSourceEntity(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->userId);
    }

    /**
     * Test that updatedAt is null when the source entity has never been edited.
     */
    public function testUpdatedAtIsNullWhenNeverEdited(): void
    {
        $this->assertNull($this->class->updatedAt);
    }
}
