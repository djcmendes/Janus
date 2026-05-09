<?php

/**
 * @file CreateDashboardHandlerHandleTest.php
 *
 * Tests for CreateDashboardHandler::handle().
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\CreateDashboardCommand;
use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use App\Dashboards\Application\DTO\DashboardDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies handle() creates a Dashboard, persists it, and returns a populated DTO.
 */
#[CoversClass(CreateDashboardHandler::class)]
final class CreateDashboardHandlerHandleTest extends CreateDashboardHandlerTest
{
    /**
     * Test that handle() calls repository->save() once.
     */
    public function testHandleCallsSaveOnce(): void
    {
        $this->repository->expects($this->once())->method('save');

        $this->class->handle(new CreateDashboardCommand('My Dashboard', null, null, 'user-uuid-001'));
    }

    /**
     * Test that handle() returns a DashboardDto.
     */
    public function testHandleReturnsDashboardDto(): void
    {
        $result = $this->class->handle(new CreateDashboardCommand('My Dashboard', 'icon', 'note', 'user-uuid-001'));

        $this->assertInstanceOf(DashboardDto::class, $result);
    }

    /**
     * Test that the returned DTO carries the dashboard name.
     */
    public function testHandleReturnsDtoWithCorrectName(): void
    {
        $result = $this->class->handle(new CreateDashboardCommand('Analytics', null, null, null));

        $this->assertSame('Analytics', $result->name);
    }

    /**
     * Test that handle() works with a null userId (shared/global dashboard).
     */
    public function testHandleWithNullUserIdCreatesSharedDashboard(): void
    {
        $result = $this->class->handle(new CreateDashboardCommand('Global', null, null, null));

        $this->assertNull($result->userId);
    }
}
