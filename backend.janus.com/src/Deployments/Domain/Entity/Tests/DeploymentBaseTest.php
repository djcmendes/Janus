<?php

/**
 * @file DeploymentBaseTest.php
 *
 * Constructor and interface compliance tests for the Deployment domain entity.
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
 * Constructor and interface compliance tests for the Deployment domain entity.
 */
#[CoversClass(Deployment::class)]
final class DeploymentBaseTest extends DeploymentTest
{
    /**
     * Test that the SUT is an instance of Deployment.
     */
    public function testIsInstanceOfDeployment(): void
    {
        $this->assertInstanceOf(Deployment::class, $this->class);
    }

    /**
     * Test that the constructor generates a valid UUIDv7 string.
     */
    public function testConstructorGeneratesUuidV7(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $this->class->getId(),
        );
    }

    /**
     * Test that the constructor stores the providerId argument.
     */
    public function testConstructorSetsProviderId(): void
    {
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $this->class->getProviderId());
    }

    /**
     * Test that the constructor stores the triggeredBy argument.
     */
    public function testConstructorSetsTriggeredBy(): void
    {
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $this->class->getTriggeredBy());
    }

    /**
     * Test that the constructor sets the initial status to PENDING.
     */
    public function testConstructorSetsStatusToPending(): void
    {
        $this->assertSame(DeploymentRunStatus::PENDING, $this->class->getStatus());
    }

    /**
     * Test that log defaults to null on construction.
     */
    public function testLogDefaultsToNull(): void
    {
        $this->assertNull($this->class->getLog());
    }

    /**
     * Test that completedAt defaults to null on construction.
     */
    public function testCompletedAtDefaultsToNull(): void
    {
        $this->assertNull($this->class->getCompletedAt());
    }

    /**
     * Test that triggeredBy defaults to null when omitted.
     */
    public function testTriggeredByDefaultsToNull(): void
    {
        $d = new Deployment('pppppppp-0000-7000-8000-000000000001');
        $this->assertNull($d->getTriggeredBy());
    }

    /**
     * Test that startedAt is set to approximately the current time.
     */
    public function testConstructorSetsStartedAtToNow(): void
    {
        $before = new \DateTimeImmutable();
        $d      = new Deployment('pppppppp-0000-7000-8000-000000000001');
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $d->getStartedAt());
        $this->assertLessThanOrEqual($after, $d->getStartedAt());
    }

    /**
     * Test that each instance receives a unique UUID.
     */
    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new Deployment('p1');
        $b = new Deployment('p2');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
