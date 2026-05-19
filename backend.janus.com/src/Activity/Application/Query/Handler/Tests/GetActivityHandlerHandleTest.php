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
#[CoversClass(className:  GetActivityHandler::class)]
#[CoversMethod(className: GetActivityHandler::class, methodName: 'handle')]
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
     * Test that handle() returns an array containing a 'filter_total' key.
     */
    public function testHandleReturnsArrayWithFilterTotalKey(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertArrayHasKey(key: 'filter_total', array: $result);
    }

    /**
     * Test that handle() returns an array containing an 'unfiltered_total' key.
     */
    public function testHandleReturnsArrayWithUnfilteredTotalKey(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertArrayHasKey(key: 'unfiltered_total', array: $result);
    }

    /**
     * Test that handle() maps paginated entities to ActivityDto instances in the data array.
     */
    public function testHandleDataContainsActivityDtos(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: [ $this->makeActivity() ]);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 1);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertCount( expectedCount: 1, haystack: $result['data']);
        $this->assertInstanceOf(expected: ActivityDto::class, actual: $result['data'][0]);
    }

    /**
     * Test that filter_total matches the filtered countAll() result.
     */
    public function testHandleFilterTotalMatchesFilteredCountAll(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturnOnConsecutiveCalls(42, 100);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertSame(expected: 42, actual: $result['filter_total']);
    }

    /**
     * Test that unfiltered_total matches the unfiltered countAll() result.
     */
    public function testHandleUnfilteredTotalMatchesUnfilteredCountAll(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturnOnConsecutiveCalls(42, 100);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertSame(expected: 100, actual: $result['unfiltered_total']);
    }

    /**
     * Test that handle() returns an empty data array and zero totals when no records exist.
     */
    public function testHandleReturnsEmptyDataArrayWhenNoRecords(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $result = $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));

        $this->assertSame(expected: [], actual: $result['data']);
        $this->assertSame(expected: 0, actual: $result['filter_total']);
        $this->assertSame(expected: 0, actual: $result['unfiltered_total']);
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
     * Test that handle() calls countAll twice — once with filters, once without.
     */
    public function testHandleCallsCountAllTwice(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $this->repository->expects($this->exactly(2))
                         ->method(constraint: 'countAll')
                         ->willReturn(value: 0);

        $this->class->handle(query: new GetActivityQuery(limit: 25, offset: 0));
    }

    /**
     * Test that handle() forwards active filters to the first countAll() call.
     */
    public function testHandleForwardsFiltersToFilteredCountAll(): void
    {
        $this->repository->method(constraint: 'findPaginated')
                         ->willReturn(value: []);

        $matcher = $this->exactly(2);
        $this->repository->expects($matcher)
                         ->method(constraint: 'countAll')
                         ->willReturnCallback(function (?string $collection = null, ?string $action = null, ?string $userId = null) use ($matcher): int {
                             if ($matcher->numberOfInvocations() === 1) {
                                 $this->assertSame('posts', $collection);
                                 $this->assertSame('create', $action);
                                 $this->assertSame('user-uuid', $userId);
                             } else {
                                 $this->assertNull($collection);
                                 $this->assertNull($action);
                                 $this->assertNull($userId);
                             }
                             return 0;
                         });

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
