<?php

/**
 * @file GetActivityHandlerHandleTest.php
 *
 * Tests for GetActivityHandler::handle().
 *
 * @package App\Activity\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Activity\Application\Query\Handler\Tests;

use App\Activity\Application\DTO\ActivityDto;
use App\Activity\Application\Query\GetActivityQuery;
use App\Activity\Application\Query\Handler\GetActivityHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Tests for GetActivityHandler::handle().
 *
 * Covers: result shape, DTO mapping, empty results, pagination and filter
 * forwarding to findPaginated() and countAll().
 */
#[CoversClass(GetActivityHandler::class)]
#[CoversMethod(GetActivityHandler::class, 'handle')]
final class GetActivityHandlerHandleTest extends GetActivityHandlerTest
{
    // ── Result shape ──────────────────────────────────────────────────────────

    /**
     * Test that handle() returns an array containing a 'data' key.
     */
    public function testHandleReturnsArrayWithDataKey(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetActivityQuery(25, 0));

        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test that handle() returns an array containing a 'total' key.
     */
    public function testHandleReturnsArrayWithTotalKey(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetActivityQuery(25, 0));

        $this->assertArrayHasKey('total', $result);
    }

    /**
     * Test that handle() maps paginated entities to ActivityDto instances in the data array.
     */
    public function testHandleDataContainsActivityDtos(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeActivity()]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->class->handle(new GetActivityQuery(25, 0));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(ActivityDto::class, $result['data'][0]);
    }

    /**
     * Test that the total value in the result matches the count returned by countAll().
     */
    public function testHandleTotalMatchesRepositoryCountAll(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->class->handle(new GetActivityQuery(25, 0));

        $this->assertSame(42, $result['total']);
    }

    /**
     * Test that handle() returns an empty data array and zero total when no records exist.
     */
    public function testHandleReturnsEmptyDataArrayWhenNoRecords(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetActivityQuery(25, 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }

    // ── Pagination forwarding ─────────────────────────────────────────────────

    /**
     * Test that handle() forwards the limit and offset from the query to findPaginated().
     */
    public function testHandleForwardsPaginationToFindPaginated(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(10, 50, null, null, null)
            ->willReturn([]);

        $this->repository->method('countAll')->willReturn(0);

        $this->class->handle(new GetActivityQuery(10, 50));
    }

    // ── Filter forwarding ─────────────────────────────────────────────────────

    /**
     * Test that handle() forwards all active filters to findPaginated().
     */
    public function testHandleForwardsFiltersToFindPaginated(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(25, 0, 'posts', 'create', 'user-uuid')
            ->willReturn([]);

        $this->repository->method('countAll')->willReturn(0);

        $this->class->handle(new GetActivityQuery(25, 0, 'posts', 'create', 'user-uuid'));
    }

    /**
     * Test that handle() forwards all active filters to countAll().
     */
    public function testHandleForwardsFiltersToCountAll(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);

        $this->repository
            ->expects($this->once())
            ->method('countAll')
            ->with('posts', 'create', 'user-uuid')
            ->willReturn(0);

        $this->class->handle(new GetActivityQuery(25, 0, 'posts', 'create', 'user-uuid'));
    }
}
