<?php

/**
 * @file GetCommentByIdHandlerHandleTest.php
 *
 * Tests for GetCommentByIdHandler::handle().
 *
 * @package App\Comments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler\Tests;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentByIdQuery;
use App\Comments\Application\Query\Handler\GetCommentByIdHandler;
use App\Comments\Domain\Exception\CommentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for GetCommentByIdHandler::handle() — verifying DTO return and not-found exception.
 */
#[CoversClass(className: GetCommentByIdHandler::class)]
#[CoversMethod(GetCommentByIdHandler::class, 'handle')]
final class GetCommentByIdHandlerHandleTest extends GetCommentByIdHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() returns a CommentDto when the comment exists.
     */
    public function testHandleReturnsCommentDtoForExistingComment(): void
    {
        $this->repository->method('findById')->willReturn($this->makeComment());

        $result = $this->class->handle(new GetCommentByIdQuery('some-uuid'));

        $this->assertInstanceOf(CommentDto::class, $result);
    }

    /**
     * Test that handle() forwards the query id to the repository findById().
     */
    public function testHandleForwardsIdToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with('some-uuid')
            ->willReturn($this->makeComment());

        $this->class->handle(new GetCommentByIdQuery('some-uuid'));
    }

    /**
     * Test that handle() maps the comment fields onto the returned DTO.
     */
    public function testHandleMapsCommentFieldsOntoDto(): void
    {
        $this->repository->method('findById')->willReturn($this->makeComment());

        $result = $this->class->handle(new GetCommentByIdQuery('some-uuid'));

        $this->assertSame('posts', $result->collection);
        $this->assertSame('42', $result->item);
        $this->assertSame('Hello world', $result->comment);
    }

    // Failure / exception paths ────────────────────────────────────

    /**
     * Test that handle() throws CommentNotFoundException when no comment exists.
     */
    public function testHandleThrowsCommentNotFoundExceptionWhenCommentMissing(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(CommentNotFoundException::class);

        $this->class->handle(new GetCommentByIdQuery('non-existent-id'));
    }
}
