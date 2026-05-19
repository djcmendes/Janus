<?php

/**
 * @file DeploymentCompleteTest.php
 *
 * Tests for Deployment::complete().
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
 * Verifies that complete() marks the run with a final status, log, and completedAt timestamp.
 */
#[CoversClass(className: Deployment::class)]
final class DeploymentCompleteTest extends DeploymentTest
{
    /**
     * Test that complete() sets the final status.
     */
    public function testCompleteSetsStatus(): void
    {
        $this->class->complete(DeploymentRunStatus::SUCCESS, '[HTTP 200] ok');
        $this->assertSame(DeploymentRunStatus::SUCCESS, $this->class->getStatus());
    }

    /**
     * Test that complete() stores the log text.
     */
    public function testCompleteSetsLog(): void
    {
        $this->class->complete(DeploymentRunStatus::SUCCESS, '[HTTP 200] ok');
        $this->assertSame('[HTTP 200] ok', $this->class->getLog());
    }

    /**
     * Test that complete() sets completedAt to approximately now.
     */
    public function testCompleteSetsCompletedAt(): void
    {
        $before = new \DateTimeImmutable();
        $this->class->complete(DeploymentRunStatus::FAILURE, null);
        $after = new \DateTimeImmutable();

        $this->assertNotNull($this->class->getCompletedAt());
        $this->assertGreaterThanOrEqual($before, $this->class->getCompletedAt());
        $this->assertLessThanOrEqual($after, $this->class->getCompletedAt());
    }

    /**
     * Test that complete() accepts a null log.
     */
    public function testCompleteAcceptsNullLog(): void
    {
        $this->class->complete(DeploymentRunStatus::FAILURE, null);
        $this->assertNull($this->class->getLog());
    }

    /**
     * Test that complete() returns fluent self.
     */
    public function testCompleteReturnsSelf(): void
    {
        $result = $this->class->complete(DeploymentRunStatus::SUCCESS, null);
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that complete() with FAILURE status stores the failure status.
     */
    public function testCompleteWithFailureStatus(): void
    {
        $this->class->complete(DeploymentRunStatus::FAILURE, '[ERROR] connection refused');
        $this->assertSame(DeploymentRunStatus::FAILURE, $this->class->getStatus());
        $this->assertSame('[ERROR] connection refused', $this->class->getLog());
    }
}
