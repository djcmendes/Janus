<?php

/**
 * @file DeploymentEntityBaseTest.php
 *
 * Getter/setter compliance tests for the DeploymentEntity Doctrine persistence model.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Getter/setter compliance tests for DeploymentEntity.
 */
#[CoversClass(DeploymentEntity::class)]
final class DeploymentEntityBaseTest extends DeploymentEntityTest
{
    /**
     * Test that the SUT is an instance of DeploymentEntity.
     */
    public function testIsInstanceOfDeploymentEntity(): void
    {
        $this->assertInstanceOf(DeploymentEntity::class, $this->class);
    }

    /**
     * Test that getId() returns a Uuid instance.
     */
    public function testGetIdReturnsUuid(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->class->getId());
    }

    /**
     * Test that setId() stores the given UUID.
     */
    public function testSetIdStoresId(): void
    {
        $uuid = Uuid::fromString('bbbbbbbb-0000-7000-8000-000000000002');
        $this->class->setId($uuid);
        $this->assertSame($uuid, $this->class->getId());
    }

    /**
     * Test that setId() returns fluent self.
     */
    public function testSetIdReturnsSelf(): void
    {
        $result = $this->class->setId(Uuid::fromString('bbbbbbbb-0000-7000-8000-000000000002'));
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getProviderId() returns the stored provider UUID string.
     */
    public function testGetProviderIdReturnsProviderId(): void
    {
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $this->class->getProviderId());
    }

    /**
     * Test that setProviderId() updates the provider UUID and returns fluent self.
     */
    public function testSetProviderIdUpdateAndReturnsSelf(): void
    {
        $result = $this->class->setProviderId('pppppppp-0000-7000-8000-000000000099');
        $this->assertSame('pppppppp-0000-7000-8000-000000000099', $this->class->getProviderId());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getStatus() returns the stored status enum.
     */
    public function testGetStatusReturnsStatus(): void
    {
        $this->assertSame(DeploymentRunStatus::SUCCESS, $this->class->getStatus());
    }

    /**
     * Test that setStatus() updates the status and returns fluent self.
     */
    public function testSetStatusUpdatesAndReturnsSelf(): void
    {
        $result = $this->class->setStatus(DeploymentRunStatus::FAILURE);
        $this->assertSame(DeploymentRunStatus::FAILURE, $this->class->getStatus());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getLog() returns the stored log text.
     */
    public function testGetLogReturnsLog(): void
    {
        $this->assertSame('[HTTP 200] ok', $this->class->getLog());
    }

    /**
     * Test that setLog() accepts null and returns fluent self.
     */
    public function testSetLogAcceptsNullAndReturnsSelf(): void
    {
        $result = $this->class->setLog(null);
        $this->assertNull($this->class->getLog());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getTriggeredBy() returns the stored user UUID.
     */
    public function testGetTriggeredByReturnsTriggeredBy(): void
    {
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $this->class->getTriggeredBy());
    }

    /**
     * Test that setTriggeredBy() accepts null and returns fluent self.
     */
    public function testSetTriggeredByAcceptsNullAndReturnsSelf(): void
    {
        $result = $this->class->setTriggeredBy(null);
        $this->assertNull($this->class->getTriggeredBy());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getStartedAt() returns a DateTimeImmutable.
     */
    public function testGetStartedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getStartedAt());
    }

    /**
     * Test that setStartedAt() stores the given timestamp and returns fluent self.
     */
    public function testSetStartedAtStoresAndReturnsSelf(): void
    {
        $ts     = new \DateTimeImmutable('2025-01-01T00:00:00Z');
        $result = $this->class->setStartedAt($ts);
        $this->assertSame($ts, $this->class->getStartedAt());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getCompletedAt() returns a DateTimeImmutable.
     */
    public function testGetCompletedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getCompletedAt());
    }

    /**
     * Test that setCompletedAt() accepts null and returns fluent self.
     */
    public function testSetCompletedAtAcceptsNullAndReturnsSelf(): void
    {
        $result = $this->class->setCompletedAt(null);
        $this->assertNull($this->class->getCompletedAt());
        $this->assertSame($this->class, $result);
    }
}
