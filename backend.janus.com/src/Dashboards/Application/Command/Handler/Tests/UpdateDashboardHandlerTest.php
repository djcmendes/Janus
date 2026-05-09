<?php

/**
 * @file UpdateDashboardHandlerTest.php
 *
 * Abstract base for UpdateDashboardHandler test suites.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for UpdateDashboardHandler test suites.
 */
#[CoversClass(UpdateDashboardHandler::class)]
abstract class UpdateDashboardHandlerTest extends TestCase
{
    /**
     * Mock of the dashboard repository.
     * @var MockObject&DashboardRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var UpdateDashboardHandler
     */
    protected UpdateDashboardHandler $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DashboardRepositoryInterface::class);
        $this->class      = new UpdateDashboardHandler(repository: $this->repository);
    }

    /**
     * Releases references after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->repository, $this->class);
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
            name:      'Original Name',
            icon:      null,
            note:      null,
            userId:    'user-uuid-001',
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
        );
    }
}
