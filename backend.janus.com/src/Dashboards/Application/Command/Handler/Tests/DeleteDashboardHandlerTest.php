<?php

/**
 * @file DeleteDashboardHandlerTest.php
 *
 * Abstract base for DeleteDashboardHandler test suites.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use App\Panels\Domain\Repository\PanelRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DeleteDashboardHandler test suites.
 */
#[CoversClass(className: DeleteDashboardHandler::class)]
abstract class DeleteDashboardHandlerTest extends TestCase
{
    /**
     * Mock of the dashboard repository.
     * @var MockObject&DashboardRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * Mock of the panel repository (used for cascade deletion).
     * @var MockObject&PanelRepositoryInterface
     */
    protected MockObject $panelRepository;

    /**
     * The system under test.
     * @var DeleteDashboardHandler
     */
    protected DeleteDashboardHandler $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->repository      = $this->createMock(DashboardRepositoryInterface::class);
        $this->panelRepository = $this->createMock(PanelRepositoryInterface::class);
        $this->class           = new DeleteDashboardHandler(
            repository:     $this->repository,
            panelRepository: $this->panelRepository,
        );
    }

    /**
     * Releases references after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->panelRepository, $this->class);
    }

    /**
     * Builds a domain Dashboard with deterministic test values.
     *
     * @return Dashboard
     */
    protected function makeDashboard(): Dashboard
    {
        return Dashboard::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Dashboard to delete',
            icon:      null,
            note:      null,
            userId:    'user-uuid-001',
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
        );
    }
}
