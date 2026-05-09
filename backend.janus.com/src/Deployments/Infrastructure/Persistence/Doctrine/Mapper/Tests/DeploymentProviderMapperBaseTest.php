<?php

/**
 * @file DeploymentProviderMapperBaseTest.php
 *
 * Instantiation tests for DeploymentProviderMapper.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentProviderMapper instantiation.
 */
#[CoversClass(DeploymentProviderMapper::class)]
final class DeploymentProviderMapperBaseTest extends DeploymentProviderMapperTest
{
    /**
     * Test that the SUT is an instance of DeploymentProviderMapper.
     */
    public function testIsInstanceOfDeploymentProviderMapper(): void
    {
        $this->assertInstanceOf(DeploymentProviderMapper::class, $this->class);
    }
}
