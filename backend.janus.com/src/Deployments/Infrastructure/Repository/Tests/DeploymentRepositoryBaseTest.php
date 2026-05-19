<?php

/**
 * @file DeploymentRepositoryBaseTest.php
 *
 * Instantiation tests for DeploymentRepository.
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentRepository instantiation.
 */
#[CoversClass(className: DeploymentRepository::class)]
final class DeploymentRepositoryBaseTest extends DeploymentRepositoryTest
{
    /**
     * Test that the SUT is an instance of DeploymentRepository.
     */
    public function testIsInstanceOfDeploymentRepository(): void
    {
        $this->assertInstanceOf(DeploymentRepository::class, $this->class);
    }
}
