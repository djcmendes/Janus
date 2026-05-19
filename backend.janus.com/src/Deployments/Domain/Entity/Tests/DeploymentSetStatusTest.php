<?php

/**
 * @file DeploymentSetStatusTest.php
 *
 * Tests for Deployment::setStatus().
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that setStatus() updates the lifecycle state and returns fluent self.
 */
#[CoversClass(className: Deployment::class)]
final class DeploymentSetStatusTest extends DeploymentTest
{
    /**
     * Test that setStatus() updates the status.
     */
    public function testSetStatusUpdatesStatus(): void
    {
        $this->class->setStatus(DeploymentRunStatus::RUNNING);
        $this->assertSame(DeploymentRunStatus::RUNNING, $this->class->getStatus());
    }

    /**
     * Test that setStatus() returns fluent self.
     */
    public function testSetStatusReturnsSelf(): void
    {
        $result = $this->class->setStatus(DeploymentRunStatus::SUCCESS);
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that setStatus() can transition from PENDING to SUCCESS.
     */
    public function testSetStatusTransitionsPendingToSuccess(): void
    {
        $this->assertSame(DeploymentRunStatus::PENDING, $this->class->getStatus());
        $this->class->setStatus(DeploymentRunStatus::SUCCESS);
        $this->assertSame(DeploymentRunStatus::SUCCESS, $this->class->getStatus());
    }

    /**
     * Test that setStatus() can set FAILURE status.
     */
    public function testSetStatusCanSetFailure(): void
    {
        $this->class->setStatus(DeploymentRunStatus::FAILURE);
        $this->assertSame(DeploymentRunStatus::FAILURE, $this->class->getStatus());
    }
}
