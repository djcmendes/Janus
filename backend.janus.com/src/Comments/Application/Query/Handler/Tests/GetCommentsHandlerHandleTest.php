<?php

/**
 * @file GetCommentsHandlerHandleTest.php
 *
 * Tests for GetCommentsHandler::handle().
 *
 * @package App\Comments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\Query\Handler\Tests;

use App\Comments\Application\DTO\CommentDto;
use App\Comments\Application\Query\GetCommentsQuery;
use App\Comments\Application\Query\Handler\GetCommentsHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for GetCommentsHandler::handle() — verifying paginated result shape and filter forwarding.
 */
#[CoversClass(className: GetCommentsHandler::class)]
#[CoversMethod(GetCommentsHandler::class, 'handle')]
final class GetCommentsHandlerHandleTest extends GetCommentsHandlerTest
{
    // Happy path ───────────────────────────────────────────────────

    /**
     * Test that handle() returns an array with 'data' and 'total' keys.
     */
    public function testHandleReturnsArrayWithDataAndTotalKeys(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetCommentsQuery(10, 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * Test that handle() maps domain Comments to CommentDto objects in the 'data' key.
     */
    public function testHandleMapsCommentsToDtos(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeComment()]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->class->handle(new GetCommentsQuery(10, 0));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(CommentDto::class, $result['data'][0]);
    }

    /**
     * Test that handle() returns the repository countAll() result as 'total'.
     */
    public function testHandleReturnsCountAllAsTotal(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->class->handle(new GetCommentsQuery(10, 0));

        $this->assertSame(42, $result['total']);
    }

    // Edge cases / branching ───────────────────────────────────────

    /**
     * Test that handle() returns an empty data array when no comments exist.
     */
    public function testHandleReturnsEmptyDataWhenNoComments(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetCommentsQuery(10, 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }

    /**
     * Test that handle() forwards collection and item filters to the repository.
     */
    public function testHandleForwardsFiltersToRepository(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(10, 0, 'posts', '42')
            ->willReturn([]);

        $this->repository->method('countAll')->willReturn(0);

        $this->class->handle(new GetCommentsQuery(10, 0, 'posts', '42'));
    }
}
