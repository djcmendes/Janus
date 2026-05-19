<?php

/**
 * @file UpdateDashboardHandlerBaseTest.php
 *
 * Constructor compliance tests for UpdateDashboardHandler.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\UpdateDashboardHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies UpdateDashboardHandler instantiation.
 */
#[CoversClass(className: UpdateDashboardHandler::class)]
final class UpdateDashboardHandlerBaseTest extends UpdateDashboardHandlerTest
{
    /**
     * Test that the SUT is an instance of UpdateDashboardHandler.
     */
    public function testIsInstanceOfUpdateDashboardHandler(): void
    {
        $this->assertInstanceOf(UpdateDashboardHandler::class, $this->class);
    }
}
