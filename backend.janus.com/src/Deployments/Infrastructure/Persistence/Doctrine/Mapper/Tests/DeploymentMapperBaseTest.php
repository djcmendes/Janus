<?php

/**
 * @file DeploymentMapperBaseTest.php
 *
 * Instantiation tests for DeploymentMapper.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentMapper instantiation.
 */
#[CoversClass(DeploymentMapper::class)]
final class DeploymentMapperBaseTest extends DeploymentMapperTest
{
    /**
     * Test that the SUT is an instance of DeploymentMapper.
     */
    public function testIsInstanceOfDeploymentMapper(): void
    {
        $this->assertInstanceOf(DeploymentMapper::class, $this->class);
    }
}
