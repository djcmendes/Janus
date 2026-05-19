<?php

/**
 * @file GetDashboardsHandlerHandleTest.php
 *
 * Tests for GetDashboardsHandler::handle().
 *
 * @package App\Dashboards\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler\Tests;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardsQuery;
use App\Dashboards\Application\Query\Handler\GetDashboardsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies handle() returns paginated DashboardDtos with the correct total count.
 */
#[CoversClass(className: GetDashboardsHandler::class)]
final class GetDashboardsHandlerHandleTest extends GetDashboardsHandlerTest
{
    /**
     * Test that handle() returns an array with 'data' and 'total' keys.
     */
    public function testHandleReturnsDataAndTotal(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetDashboardsQuery(25, 0));

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    /**
     * Test that handle() returns DashboardDto instances inside 'data'.
     */
    public function testHandleReturnsDtoInstances(): void
    {
        $this->repository->method('findPaginated')->willReturn([$this->makeDashboard()]);
        $this->repository->method('countAll')->willReturn(1);

        $result = $this->class->handle(new GetDashboardsQuery(25, 0));

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(DashboardDto::class, $result['data'][0]);
    }

    /**
     * Test that handle() returns the total from countAll.
     */
    public function testHandleReturnsTotalCount(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(42);

        $result = $this->class->handle(new GetDashboardsQuery(25, 0));

        $this->assertSame(42, $result['total']);
    }

    /**
     * Test that handle() passes the userId filter to the repository.
     */
    public function testHandlePassesUserIdFilter(): void
    {
        $this->repository->expects($this->once())
                         ->method('findPaginated')
                         ->with(10, 0, 'user-uuid-001')
                         ->willReturn([]);

        $this->repository->method('countAll')->willReturn(0);

        $this->class->handle(new GetDashboardsQuery(10, 0, 'user-uuid-001'));
    }

    /**
     * Test that handle() returns an empty data array when there are no dashboards.
     */
    public function testHandleReturnsEmptyDataWhenNone(): void
    {
        $this->repository->method('findPaginated')->willReturn([]);
        $this->repository->method('countAll')->willReturn(0);

        $result = $this->class->handle(new GetDashboardsQuery(25, 0));

        $this->assertSame([], $result['data']);
        $this->assertSame(0, $result['total']);
    }
}
