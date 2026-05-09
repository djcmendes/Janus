<?php

/**
 * @file CreateDashboardHandlerTest.php
 *
 * Abstract base for CreateDashboardHandler test suites.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use App\Dashboards\Domain\Repository\DashboardRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for CreateDashboardHandler test suites.
 */
#[CoversClass(CreateDashboardHandler::class)]
abstract class CreateDashboardHandlerTest extends TestCase
{
    /**
     * Mock of the dashboard repository used by the handler.
     * @var MockObject&DashboardRepositoryInterface
     */
    protected MockObject $repository;

    /**
     * The system under test.
     * @var CreateDashboardHandler
     */
    protected CreateDashboardHandler $class;

    /**
     * Builds the SUT before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->repository = $this->createMock(DashboardRepositoryInterface::class);
        $this->class      = new CreateDashboardHandler(repository: $this->repository);
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
}
