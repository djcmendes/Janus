<?php

/**
 * @file GetDeploymentByIdHandlerBaseTest.php
 *
 * Instantiation tests for GetDeploymentByIdHandler.
 *
 * @package App\Deployments\Application\Query\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Query\Handler\Tests;

use App\Deployments\Application\Query\Handler\GetDeploymentByIdHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies GetDeploymentByIdHandler instantiation.
 */
#[CoversClass(className: GetDeploymentByIdHandler::class)]
final class GetDeploymentByIdHandlerBaseTest extends GetDeploymentByIdHandlerTest
{
    /**
     * Test that the SUT is an instance of GetDeploymentByIdHandler.
     */
    public function testIsInstanceOfGetDeploymentByIdHandler(): void
    {
        $this->assertInstanceOf(GetDeploymentByIdHandler::class, $this->class);
    }
}
