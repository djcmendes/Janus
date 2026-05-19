<?php

/**
 * @file DeleteDashboardHandlerBaseTest.php
 *
 * Constructor compliance tests for DeleteDashboardHandler.
 *
 * @package App\Dashboards\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Application\Command\Handler\Tests;

use App\Dashboards\Application\Command\Handler\DeleteDashboardHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeleteDashboardHandler instantiation.
 */
#[CoversClass(className: DeleteDashboardHandler::class)]
final class DeleteDashboardHandlerBaseTest extends DeleteDashboardHandlerTest
{
    /**
     * Test that the SUT is an instance of DeleteDashboardHandler.
     */
    public function testIsInstanceOfDeleteDashboardHandler(): void
    {
        $this->assertInstanceOf(DeleteDashboardHandler::class, $this->class);
    }
}
