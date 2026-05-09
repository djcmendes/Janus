<?php

/**
 * @file TriggerDeploymentHandlerBaseTest.php
 *
 * Instantiation tests for TriggerDeploymentHandler.
 *
 * @package App\Deployments\Application\Command\Handler\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\Command\Handler\Tests;

use App\Deployments\Application\Command\Handler\TriggerDeploymentHandler;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies TriggerDeploymentHandler instantiation.
 */
#[CoversClass(TriggerDeploymentHandler::class)]
final class TriggerDeploymentHandlerBaseTest extends TriggerDeploymentHandlerTest
{
    /**
     * Test that the SUT is an instance of TriggerDeploymentHandler.
     */
    public function testIsInstanceOfTriggerDeploymentHandler(): void
    {
        $this->assertInstanceOf(TriggerDeploymentHandler::class, $this->class);
    }
}
