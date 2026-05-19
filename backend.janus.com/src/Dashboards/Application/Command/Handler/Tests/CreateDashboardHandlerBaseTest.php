<?php

/**
 * @file CreateDashboardHandlerBaseTest.php
 *
 * Constructor compliance tests for CreateDashboardHandler.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\CreateDashboardHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies CreateDashboardHandler instantiation.
 */
#[CoversClass(className: CreateDashboardHandler::class)]
final class CreateDashboardHandlerBaseTest extends CreateDashboardHandlerTest
{
    /**
     * Test that the SUT is an instance of CreateDashboardHandler.
     */
    public function testIsInstanceOfCreateDashboardHandler(): void
    {
        $this->assertInstanceOf(CreateDashboardHandler::class, $this->class);
    }
}
