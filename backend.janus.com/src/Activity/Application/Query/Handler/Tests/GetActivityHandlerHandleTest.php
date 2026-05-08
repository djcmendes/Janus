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
    /**
     * Test that handle() returns an array containing a 'data' key.
     */
    public function testHandleReturnsArrayWithDataKey(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertArrayHasKey(key: 'data', array: $result);
    }

    /**
     * Test that handle() returns an array containing a 'total' key.
     */
    public function testHandleReturnsArrayWithTotalKey(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertArrayHasKey(key: 'total', array: $result);
    }

    /**
     * Test that handle() maps paginated entities to ActivityDto instances in the data array.
     */
    public function testHandleDataContainsActivityDtos(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: [$this->makeActivity()]);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 1);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertCount( expectedCount: 1, haystack: $result['data']);
        $this->assertInstanceOf(expected: ActivityDto::class, actual: $result['data'][0]);
    }

    /**
     * Test that the total value in the result matches the count returned by countAll().
     */
    public function testHandleTotalMatchesRepositoryCountAll(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 42);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertSame(expected: 42, actual: $result['total']);
    }

    /**
     * Test that handle() returns an empty data array and zero total when no records exist.
     */
    public function testHandleReturnsEmptyDataArrayWhenNoRecords(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertSame(expected: [], actual: $result['data']);
        $this->assertSame(expected: 0, actual: $result['total']);
    }

    /**
     * Test that handle() forwards the limit and offset from the query to findPaginated().
     */
    public function testHandleForwardsPaginationToFindPaginated(): void
    {
        $this->repository->expects($this->once())
                         ->method(constraint: 'findPaginated')
                         ->with(10, 50, null, null, null)
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $this->class->handle(query: new GetActivityQuery(limit: 10, offset: 50));
    }

    /**
     * Test that handle() forwards all active filters to findPaginated().
     */
    public function testHandleForwardsFiltersToFindPaginated(): void
    {
        $this->repository->expects($this->once())
                         ->method(constraint: 'findPaginated')
                         ->with(25, 0, 'posts', 'create', 'user-uuid')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $this->class->handle(
            query: new GetActivityQuery(
                limit: 25,
                offset: 0,
                collection: 'posts',
                action: 'create',
                userId: 'user-uuid'
            )
        );
    }

    /**
     * Test that handle() forwards all active filters to countAll().
     */
    public function testHandleForwardsFiltersToCountAll(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->expects($this->once())
                         ->method(constraint: 'countAll')
                         ->with('posts', 'create', 'user-uuid')
                         ->willReturn(value: 0);

        $this->class->handle(
            query: new GetActivityQuery(
                limit: 25,
                offset: 0,
                collection: 'posts',
                action: 'create',
                userId: 'user-uuid'
            )
        );
    }
}
