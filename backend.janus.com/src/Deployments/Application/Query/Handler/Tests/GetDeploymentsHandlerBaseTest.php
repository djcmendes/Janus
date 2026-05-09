<?php

/**
 * @file GetDeploymentsHandlerBaseTest.php
 *
 * Instantiation tests for GetDeploymentsHandler.
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\Query\Handler\GetDeploymentsHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetDeploymentsHandler instantiation.
 */
#[CoversClass(GetDeploymentsHandler::class)]
final class GetDeploymentsHandlerBaseTest extends GetDeploymentsHandlerTest
{
    /**
     * Test that the SUT is an instance of GetDeploymentsHandler.
     */
    public function testIsInstanceOfGetDeploymentsHandler(): void
    {
        $this->assertInstanceOf(GetDeploymentsHandler::class, $this->class);
    }
}
