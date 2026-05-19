<?php

/**
 * @file DeploymentProviderRepositoryBaseTest.php
 *
 * Instantiation tests for DeploymentProviderRepository.
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentProviderRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentProviderRepository instantiation.
 */
#[CoversClass(className: DeploymentProviderRepository::class)]
final class DeploymentProviderRepositoryBaseTest extends DeploymentProviderRepositoryTest
{
    /**
     * Test that the SUT is an instance of DeploymentProviderRepository.
     */
    public function testIsInstanceOfDeploymentProviderRepository(): void
    {
        $this->assertInstanceOf(DeploymentProviderRepository::class, $this->class);
    }
}
