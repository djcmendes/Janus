<?php

/**
 * @file UpdateDashboardHandlerHandleTest.php
 *
 * Tests for UpdateDashboardHandler::handle().
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use App\Dashboards\Application\Command\UpdateDashboardCommand;
use App\Dashboards\Application\DTO\DashboardDto;
use App\Dashboards\Domain\Exception\DashboardNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies handle() applies partial updates to the Dashboard and throws when not found.
 */
#[CoversClass(className: UpdateDashboardHandler::class)]
final class UpdateDashboardHandlerHandleTest extends UpdateDashboardHandlerTest
{
    /**
     * Test that handle() throws DashboardNotFoundException when the dashboard does not exist.
     */
    public function testHandleThrowsWhenDashboardNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(DashboardNotFoundException::class);

        $this->class->handle(new UpdateDashboardCommand(
            'non-existent',
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
        ));
    }

    /**
     * Test that handle() returns a DashboardDto when found.
     */
    public function testHandleReturnsDashboardDto(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new UpdateDashboardCommand(
            'aaaaaaaa-0000-7000-8000-000000000001',
            'New Name',
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
        ));

        $this->assertInstanceOf(DashboardDto::class, $result);
    }

    /**
     * Test that handle() applies the name when provided.
     */
    public function testHandleUpdatesNameWhenProvided(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new UpdateDashboardCommand(
            'aaaaaaaa-0000-7000-8000-000000000001',
            'Updated Name',
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
        ));

        $this->assertSame('Updated Name', $result->name);
    }

    /**
     * Test that handle() leaves the name unchanged when the sentinel is set.
     */
    public function testHandlePreservesNameWhenSentinelSet(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());

        $result = $this->class->handle(new UpdateDashboardCommand(
            'aaaaaaaa-0000-7000-8000-000000000001',
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
        ));

        $this->assertSame('Original Name', $result->name);
    }

    /**
     * Test that handle() calls repository->save() once.
     */
    public function testHandleCallsSaveOnce(): void
    {
        $this->repository->method('findById')->willReturn($this->makeDashboard());
        $this->repository->expects($this->once())->method('save');

        $this->class->handle(new UpdateDashboardCommand(
            'aaaaaaaa-0000-7000-8000-000000000001',
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
            UpdateDashboardCommand::UNCHANGED,
        ));
    }
}
