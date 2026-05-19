<?php

/**
 * @file GetDashboardsHandlerBaseTest.php
 *
 * Constructor compliance tests for GetDashboardsHandler.
 *
 * @package App\Dashboards\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler\Tests;

use App\Dashboards\Application\Query\Handler\GetDashboardsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetDashboardsHandler instantiation.
 */
#[CoversClass(className: GetDashboardsHandler::class)]
final class GetDashboardsHandlerBaseTest extends GetDashboardsHandlerTest
{
    /**
     * Test that the SUT is an instance of GetDashboardsHandler.
     */
    public function testIsInstanceOfGetDashboardsHandler(): void
    {
        $this->assertInstanceOf(GetDashboardsHandler::class, $this->class);
    }
}
