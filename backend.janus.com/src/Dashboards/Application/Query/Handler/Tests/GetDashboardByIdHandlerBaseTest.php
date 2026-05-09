<?php

/**
 * @file GetDashboardByIdHandlerBaseTest.php
 *
 * Constructor compliance tests for GetDashboardByIdHandler.
 *
 * @package App\Dashboards\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Query\Handler\Tests;

use App\Dashboards\Application\Query\Handler\GetDashboardByIdHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetDashboardByIdHandler instantiation.
 */
#[CoversClass(GetDashboardByIdHandler::class)]
final class GetDashboardByIdHandlerBaseTest extends GetDashboardByIdHandlerTest
{
    /**
     * Test that the SUT is an instance of GetDashboardByIdHandler.
     */
    public function testIsInstanceOfGetDashboardByIdHandler(): void
    {
        $this->assertInstanceOf(GetDashboardByIdHandler::class, $this->class);
    }
}
