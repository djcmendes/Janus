<?php

/**
 * @file DeploymentReconstituteTest.php
 *
 * Tests for Deployment::reconstitute().
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
 * Verifies that reconstitute() restores a Deployment from persisted state without side effects.
 */
#[CoversClass(Deployment::class)]
final class DeploymentReconstituteTest extends DeploymentTest
{
    /**
     * Test that reconstitute() preserves the given ID.
     */
    public function testReconstitutePreservesId(): void
    {
        $d = $this->makeReconstituted(id: 'aaaaaaaa-0000-7000-8000-000000000001');
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $d->getId());
    }

    /**
     * Test that reconstitute() preserves the provider ID.
     */
    public function testReconstitutePreservesProviderId(): void
    {
        $d = $this->makeReconstituted(providerId: 'pppppppp-0000-7000-8000-000000000099');
        $this->assertSame('pppppppp-0000-7000-8000-000000000099', $d->getProviderId());
    }

    /**
     * Test that reconstitute() preserves the status.
     */
    public function testReconstitutePreservesStatus(): void
    {
        $d = $this->makeReconstituted(status: DeploymentRunStatus::FAILURE);
        $this->assertSame(DeploymentRunStatus::FAILURE, $d->getStatus());
    }

    /**
     * Test that reconstitute() preserves the log.
     */
    public function testReconstitutePreservesLog(): void
    {
        $d = $this->makeReconstituted(log: '[HTTP 404] not found');
        $this->assertSame('[HTTP 404] not found', $d->getLog());
    }

    /**
     * Test that reconstitute() preserves a null log.
     */
    public function testReconstitutePreservesNullLog(): void
    {
        $d = $this->makeReconstituted(log: null);
        $this->assertNull($d->getLog());
    }

    /**
     * Test that reconstitute() preserves the triggeredBy UUID.
     */
    public function testReconstitutePreservesTriggeredBy(): void
    {
        $d = $this->makeReconstituted(triggeredBy: 'uuuuuuuu-0000-7000-8000-000000000099');
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000099', $d->getTriggeredBy());
    }

    /**
     * Test that reconstitute() preserves the startedAt timestamp.
     */
    public function testReconstitutePreservesStartedAt(): void
    {
        $ts = new \DateTimeImmutable('2023-06-15T10:00:00Z');
        $d  = $this->makeReconstituted(startedAt: $ts);
        $this->assertSame($ts, $d->getStartedAt());
    }

    /**
     * Test that reconstitute() preserves a non-null completedAt timestamp.
     */
    public function testReconstitutePreservesCompletedAt(): void
    {
        $ts = new \DateTimeImmutable('2023-06-15T10:01:00Z');
        $d  = $this->makeReconstituted(completedAt: $ts);
        $this->assertSame($ts, $d->getCompletedAt());
    }

    /**
     * Test that reconstitute() preserves a null completedAt.
     */
    public function testReconstitutePreservesNullCompletedAt(): void
    {
        $d = $this->makeReconstituted(completedAt: null);
        $this->assertNull($d->getCompletedAt());
    }

    /**
     * Test that reconstitute() returns a Deployment instance.
     */
    public function testReconstituteReturnsDeployment(): void
    {
        $this->assertInstanceOf(Deployment::class, $this->makeReconstituted());
    }
}
