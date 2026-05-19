<?php

/**
 * @file CreateDeploymentHandlerBaseTest.php
 *
 * Instantiation tests for CreateDeploymentHandler.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\CreateDeploymentHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies CreateDeploymentHandler instantiation.
 */
#[CoversClass(className: CreateDeploymentHandler::class)]
final class CreateDeploymentHandlerBaseTest extends CreateDeploymentHandlerTest
{
    /**
     * Test that the SUT is an instance of CreateDeploymentHandler.
     */
    public function testIsInstanceOfCreateDeploymentHandler(): void
    {
        $this->assertInstanceOf(CreateDeploymentHandler::class, $this->class);
    }
}
