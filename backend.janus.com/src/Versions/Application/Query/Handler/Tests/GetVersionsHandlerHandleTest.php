<?php

/**
 * @file GetVersionsHandlerHandleTest.php
 *
 * Tests for GetVersionsHandler::handle().
 *
 * @package App\Versions\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\Query\Handler\Tests;

use App\Versions\Application\DTO\VersionDto;
use App\Versions\Application\Query\GetVersionsQuery;
use App\Versions\Application\Query\Handler\GetVersionsHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * Verifies that handle() delegates to the repository, maps results to DTOs, and returns totals.
 */
#[CoversClass(GetVersionsHandler::class)]
#[CoversMethod(GetVersionsHandler::class, 'handle')]
final class GetVersionsHandlerHandleTest extends GetVersionsHandlerTest
{
    /**
     * Test that handle() returns an array with 'data' and 'total' keys.
     */
    public function testHandleReturnsDataAndTotalKeys(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetVersionsQuery(25, 0, null, null));

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * Test that handle() maps each Version to a VersionDto.
     */
    public function testHandleMapsVersionsToDtos(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeVersion()]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->class->handle(new GetVersionsQuery(25, 0, null, null));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(VersionDto::class, $result['data'][0]);
    }

    /**
     * Test that handle() returns the total from countAll().
     */
    public function testHandleReturnsTotalFromRepository(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->class->handle(new GetVersionsQuery(25, 0, null, null));

        $this->assertSame(42, $result['total']);
    }

    /**
     * Test that handle() passes limit and offset to findPaginated.
     */
    public function testHandleForwardsLimitAndOffset(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(10, 5, null, null)
            ->willReturn([]);

        $this->repository->method('countAll')->willReturn(0);

        $this->class->handle(new GetVersionsQuery(10, 5, null, null));
    }

    /**
     * Test that handle() passes collection and item filters to both repository methods.
     */
    public function testHandleForwardsFilters(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('findPaginated')
            ->with(25, 0, 'articles', 'item-uuid-1')
            ->willReturn([]);

        $this->repository
            ->expects($this->once())
            ->method('countAll')
            ->with('articles', 'item-uuid-1')
            ->willReturn(0);

        $this->class->handle(new GetVersionsQuery(25, 0, 'articles', 'item-uuid-1'));
    }

    /**
     * Test that handle() returns empty data array when repository returns no results.
     */
    public function testHandleReturnsEmptyDataForEmptyRepository(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetVersionsQuery(25, 0, null, null));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }
}
