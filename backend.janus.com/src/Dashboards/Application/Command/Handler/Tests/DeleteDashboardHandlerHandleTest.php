<?php

/**
 * @file DeleteDashboardHandlerHandleTest.php
 *
 * Tests for DeleteDashboardHandler::handle().
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\DeleteDashboardCommand;
use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies handle() cascade-deletes panels, removes the dashboard, and throws when not found.
 */
#[CoversClass(DeleteDashboardHandler::class)]
final class DeleteDashboardHandlerHandleTest extends DeleteDashboardHandlerTest
{
    /**
     * Test that handle() throws DashboardNotFoundException when the dashboard does not exist.
     */
    public function testHandleThrowsWhenDashboardNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(DashboardNotFoundException::class);

        $this->class->handle(new DeleteDashboardCommand('non-existent'));
    }

    /**
     * Test that handle() calls panelRepository->deleteByDashboard() with the dashboard ID.
     */
    public function testHandleDeletesPanelsByDashboardId(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $this->panelRepository->expects($this->once())
                              ->method('deleteByDashboard')
                              ->with('aaaaaaaa-0000-7000-8000-000000000001');

        $this->class->handle(new DeleteDashboardCommand('aaaaaaaa-0000-7000-8000-000000000001'));
    }

    /**
     * Test that handle() calls repository->delete() after cascade-deleting panels.
     */
    public function testHandleDeletesDashboard(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());
        $this->repository->expects($this->once())->method('delete');

        $this->class->handle(new DeleteDashboardCommand('aaaaaaaa-0000-7000-8000-000000000001'));
    }

    /**
     * Test that handle() returns void.
     */
    public function testHandleReturnsVoid(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new DeleteDashboardCommand('aaaaaaaa-0000-7000-8000-000000000001'));

        $this->assertNull($result);
    }
}
