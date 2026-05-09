<?php

/**
 * @file GetDashboardByIdHandlerHandleTest.php
 *
 * Tests for GetDashboardByIdHandler::handle().
 *
 * @package App\Dashboards\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler\Tests;

use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Application\Query\GetDashboardByIdQuery;
use App\Dashboards\Application\Query\Handler\GetDashboardByIdHandler;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies handle() returns a DashboardDto when found and throws when not found.
 */
#[CoversClass(GetDashboardByIdHandler::class)]
final class GetDashboardByIdHandlerHandleTest extends GetDashboardByIdHandlerTest
{
    /**
     * Test that handle() returns a DashboardDto when the dashboard exists.
     */
    public function testHandleReturnsDashboardDto(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new GetDashboardByIdQuery('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertInstanceOf(DashboardDto::class, $result);
    }

    /**
     * Test that handle() maps the dashboard ID into the returned DTO.
     */
    public function testHandleReturnsDtoWithCorrectId(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new GetDashboardByIdQuery('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->id);
    }

    /**
     * Test that handle() throws DashboardNotFoundException when the dashboard does not exist.
     */
    public function testHandleThrowsWhenNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(DashboardNotFoundException::class);

        $this->class->handle(new GetDashboardByIdQuery('non-existent'));
    }
}
