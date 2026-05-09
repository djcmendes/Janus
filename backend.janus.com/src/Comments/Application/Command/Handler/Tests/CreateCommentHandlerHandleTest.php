<?php

/**
 * @file CreateCommentHandlerHandleTest.php
 *
 * Tests for CreateCommentHandler::handle().
 *
 * @package App\Comments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Command\Handler\Tests;

use App\Comments\Application\Command\CreateCommentCommand;
use App\Comments\Application\Command\Handler\CreateCommentHandler;
use App\Comments\Application\DTO\CommentDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for CreateCommentHandler::handle() — verifying save delegation and DTO return.
 */
#[CoversClass(CreateCommentHandler::class)]
#[CoversMethod(CreateCommentHandler::class, 'handle')]
final class CreateCommentHandlerHandleTest extends CreateCommentHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() returns a CommentDto.
     */
    public function testHandleReturnsCommentDto(): void
    {
        $this->repository->method('save');

        $result = $this->class->handle(
            new CreateCommentCommand('posts', '42', 'Hello world', 'user-uuid')
        );

        $this->assertInstanceOf(CommentDto::class, $result);
    }

    /**
     * Test that handle() calls repository save() exactly once.
     */
    public function testHandleCallsSaveOnRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save');

        $this->class->handle(
            new CreateCommentCommand('posts', '42', 'Hello world', 'user-uuid')
        );
    }

    /**
     * Test that handle() maps the command fields onto the returned DTO.
     */
    public function testHandleMapsCommandFieldsOntoDto(): void
    {
        $this->repository->method('save');

        $result = $this->class->handle(
            new CreateCommentCommand('articles', '99', 'Great article', 'author-uuid')
        );

        $this->assertSame('articles', $result->collection);
        $this->assertSame('99', $result->item);
        $this->assertSame('Great article', $result->comment);
        $this->assertSame('author-uuid', $result->userId);
    }
}
