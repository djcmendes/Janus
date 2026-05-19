<?php

/**
 * @file CommentDtoFromEntityTest.php
 *
 * Tests for CommentDto::fromEntity().
 *
 * @package App\Comments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\DTO\Tests;

use App\Comments\Application\DTO\CommentDto;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CommentDto::fromEntity() — verifying field extraction from the domain entity.
 */
#[CoversClass(className: CommentDto::class)]
#[CoversMethod(CommentDto::class, 'fromEntity')]
final class CommentDtoFromEntityTest extends CommentDtoTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that fromEntity() returns a CommentDto instance.
     */
    public function testFromEntityReturnsCommentDto(): void
    {
        $this->assertInstanceOf(CommentDto::class, CommentDto::fromEntity($this->makeComment()));
    }

    /**
     * Test that fromEntity() extracts the entity id as a string.
     */
    public function testFromEntityExtractsIdAsString(): void
    {
        $comment = $this->makeComment();
        $dto     = CommentDto::fromEntity($comment);

        $this->assertSame($comment->getId(), $dto->id);
    }

    /**
     * Test that fromEntity() extracts the collection correctly.
     */
    public function testFromEntityExtractsCollection(): void
    {
        $this->assertSame('posts', CommentDto::fromEntity($this->makeComment())->collection);
    }

    /**
     * Test that fromEntity() extracts the item correctly.
     */
    public function testFromEntityExtractsItem(): void
    {
        $this->assertSame('42', CommentDto::fromEntity($this->makeComment())->item);
    }

    /**
     * Test that fromEntity() extracts the comment text correctly.
     */
    public function testFromEntityExtractsComment(): void
    {
        $this->assertSame('Hello world', CommentDto::fromEntity($this->makeComment())->comment);
    }

    /**
     * Test that fromEntity() formats createdAt as an ATOM string.
     */
    public function testFromEntityFormatsCreatedAtAsAtomString(): void
    {
        $comment = $this->makeComment();
        $dto     = CommentDto::fromEntity($comment);

        $this->assertSame(
            $comment->getCreatedAt()->format(DateTimeInterface::ATOM),
            $dto->createdAt,
        );
    }

    /**
     * Test that fromEntity() sets updatedAt to null when the entity has never been edited.
     */
    public function testFromEntitySetsNullUpdatedAtWhenNeverEdited(): void
    {
        $this->assertNull(CommentDto::fromEntity($this->makeComment())->updatedAt);
    }

    /**
     * Test that fromEntity() formats updatedAt as an ATOM string after an edit.
     */
    public function testFromEntityFormatsUpdatedAtAfterEdit(): void
    {
        $comment = $this->makeComment();
        $comment->setComment('Edited');

        $dto = CommentDto::fromEntity($comment);

        $this->assertNotNull($dto->updatedAt);
        $this->assertSame(
            $comment->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $dto->updatedAt,
        );
    }
}
