<?php

/**
 * @file DashboardsControllerBaseTest.php
 *
 * Constructor and interface compliance tests for DashboardsController.
 *
 * @package App\Dashboards\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Presentation\Controller\Tests;

use App\Dashboards\Presentation\Controller\DashboardsController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DashboardsController instantiation.
 */
#[CoversClass(className: DashboardsController::class)]
final class DashboardsControllerBaseTest extends DashboardsControllerTest
{
    /**
     * Test that the SUT is an instance of DashboardsController.
     */
    public function testIsInstanceOfDashboardsController(): void
    {
        $this->assertInstanceOf(DashboardsController::class, $this->class);
    }
}
