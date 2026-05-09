<?php

/**
 * @file UpdateCommentHandlerHandleTest.php
 *
 * Tests for UpdateCommentHandler::handle().
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\Handler\UpdateCommentHandler;
use App\Comments\Application\Command\UpdateCommentCommand;
use App\Comments\Application\DTO\CommentDto;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for UpdateCommentHandler::handle() — verifying ownership enforcement,
 * admin bypass, not-found exception, and DTO return.
 */
#[CoversClass(UpdateCommentHandler::class)]
#[CoversMethod(UpdateCommentHandler::class, 'handle')]
final class UpdateCommentHandlerHandleTest extends UpdateCommentHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() returns a CommentDto when the owner updates their own comment.
     */
    public function testHandleReturnsCommentDtoForOwner(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);
        $this->repository->method('save');

        $result = $this->class->handle(
            new UpdateCommentCommand($comment->getId(), 'Updated text', 'owner-uuid', false)
        );

        $this->assertInstanceOf(CommentDto::class, $result);
        $this->assertSame('Updated text', $result->comment);
    }

    /**
     * Test that handle() allows an admin to update any comment regardless of ownership.
     */
    public function testHandleAllowsAdminToUpdateAnyComment(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);
        $this->repository->method('save');

        $result = $this->class->handle(
            new UpdateCommentCommand($comment->getId(), 'Admin edit', 'admin-uuid', true)
        );

        $this->assertInstanceOf(CommentDto::class, $result);
    }

    /**
     * Test that handle() calls save() after updating the comment text.
     */
    public function testHandleCallsSaveAfterUpdate(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);

        $this->repository
            ->expects($this->once())
            ->method('save');

        $this->class->handle(
            new UpdateCommentCommand($comment->getId(), 'New text', 'owner-uuid', false)
        );
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that handle() throws CommentNotFoundException when the comment does not exist.
     */
    public function testHandleThrowsCommentNotFoundExceptionWhenCommentMissing(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(CommentNotFoundException::class);

        $this->class->handle(
            new UpdateCommentCommand('non-existent-id', 'Text', 'user-uuid', false)
        );
    }

    /**
     * Test that handle() throws CommentForbiddenException when a non-owner non-admin tries to update.
     */
    public function testHandleThrowsCommentForbiddenExceptionForNonOwner(): void
    {
        $comment = $this->makeComment('owner-uuid');
        $this->repository->method('findById')->willReturn($comment);

        $this->expectException(CommentForbiddenException::class);

        $this->class->handle(
            new UpdateCommentCommand($comment->getId(), 'Hijack', 'other-user-uuid', false)
        );
    }
}
