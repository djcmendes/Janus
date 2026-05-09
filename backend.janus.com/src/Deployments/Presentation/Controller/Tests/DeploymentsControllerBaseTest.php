<?php

/**
 * @file DeploymentsControllerBaseTest.php
 *
 * Constructor and interface compliance tests for DeploymentsController.
 *
 * @package App\Deployments\Presentation\Controller\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Presentation\Controller\Tests;

use App\Deployments\Presentation\Controller\DeploymentsController;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentsController instantiation.
 */
#[CoversClass(DeploymentsController::class)]
final class DeploymentsControllerBaseTest extends DeploymentsControllerTest
{
    /**
     * Test that the SUT is an instance of DeploymentsController.
     */
    public function testIsInstanceOfDeploymentsController(): void
    {
        $this->assertInstanceOf(DeploymentsController::class, $this->class);
    }
}
