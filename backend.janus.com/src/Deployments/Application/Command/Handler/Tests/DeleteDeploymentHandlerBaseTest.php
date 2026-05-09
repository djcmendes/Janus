<?php

/**
 * @file DeleteDeploymentHandlerBaseTest.php
 *
 * Instantiation tests for DeleteDeploymentHandler.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\DeleteDeploymentHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeleteDeploymentHandler instantiation.
 */
#[CoversClass(DeleteDeploymentHandler::class)]
final class DeleteDeploymentHandlerBaseTest extends DeleteDeploymentHandlerTest
{
    /**
     * Test that the SUT is an instance of DeleteDeploymentHandler.
     */
    public function testIsInstanceOfDeleteDeploymentHandler(): void
    {
        $this->assertInstanceOf(DeleteDeploymentHandler::class, $this->class);
    }
}
