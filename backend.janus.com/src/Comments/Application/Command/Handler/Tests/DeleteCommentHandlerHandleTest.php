<?php

/**
 * @file DeleteCommentHandlerHandleTest.php
 *
 * Tests for DeleteCommentHandler::handle().
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\DeleteCommentCommand;
use App\Comments\Application\Command\Handler\DeleteCommentHandler;
use App\Comments\Domain\Exception\CommentForbiddenException;
use App\Comments\Domain\Exception\CommentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for DeleteCommentHandler::handle() — verifying ownership enforcement,
 * admin bypass, not-found exception, and void return.
 */
#[CoversClass(DeleteCommentHandler::class)]
#[CoversMethod(DeleteCommentHandler::class, 'handle')]
final class DeleteCommentHandlerHandleTest extends DeleteCommentHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() calls delete() on the repository when the owner deletes their comment.
     */
    public function testHandleCallsDeleteForOwner(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);

        $this->repository
            ->expects($this->once())
            ->method('delete');

        $this->class->handle(new DeleteCommentCommand($comment->getId(), 'owner-uuid', false));
    }

    /**
     * Test that handle() allows an admin to delete any comment regardless of ownership.
     */
    public function testHandleAllowsAdminToDeleteAnyComment(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);

        $this->repository
            ->expects($this->once())
            ->method('delete');

        $this->class->handle(new DeleteCommentCommand($comment->getId(), 'admin-uuid', true));
    }

    /**
     * Test that handle() returns void and produces no output.
     */
    public function testHandleReturnsVoid(): void
    {
        $comment = $this->makeComment('owner-uuid');

        $this->repository->method('findById')->willReturn($comment);
        $this->repository->method('delete');

        $result = $this->class->handle(
            new DeleteCommentCommand($comment->getId(), 'owner-uuid', false)
        );

        $this->assertNull($result);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that handle() throws CommentNotFoundException when the comment does not exist.
     */
    public function testHandleThrowsCommentNotFoundExceptionWhenCommentMissing(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(CommentNotFoundException::class);

        $this->class->handle(new DeleteCommentCommand('non-existent-id', 'user-uuid', false));
    }

    /**
     * Test that handle() throws CommentForbiddenException when a non-owner non-admin tries to delete.
     */
    public function testHandleThrowsCommentForbiddenExceptionForNonOwner(): void
    {
        $comment = $this->makeComment('owner-uuid');
        $this->repository->method('findById')->willReturn($comment);

        $this->expectException(CommentForbiddenException::class);

        $this->class->handle(new DeleteCommentCommand($comment->getId(), 'other-user-uuid', false));
    }
}
