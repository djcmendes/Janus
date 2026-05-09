<?php

/**
 * @file DeploymentMapperToPersistenceTest.php
 *
 * Tests for DeploymentMapper::toPersistence().
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Verifies that toPersistence() accurately converts a domain Deployment to a DeploymentEntity.
 */
#[CoversClass(DeploymentMapper::class)]
final class DeploymentMapperToPersistenceTest extends DeploymentMapperTest
{
    /**
     * Test that toPersistence() returns a DeploymentEntity instance.
     */
    public function testToPersistenceReturnsDeploymentEntity(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertInstanceOf(DeploymentEntity::class, $entity);
    }

    /**
     * Test that toPersistence() maps the ID as a Uuid value object.
     */
    public function testToPersistenceMapsIdAsUuid(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertInstanceOf(Uuid::class, $entity->getId());
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', (string) $entity->getId());
    }

    /**
     * Test that toPersistence() maps the provider ID string.
     */
    public function testToPersistenceMapsProviderId(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $entity->getProviderId());
    }

    /**
     * Test that toPersistence() maps the status enum.
     */
    public function testToPersistenceMapsStatus(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame(DeploymentRunStatus::SUCCESS, $entity->getStatus());
    }

    /**
     * Test that toPersistence() maps the log text.
     */
    public function testToPersistenceMapsLog(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame('[HTTP 200] ok', $entity->getLog());
    }

    /**
     * Test that toPersistence() maps the triggeredBy UUID.
     */
    public function testToPersistenceMapsTriggeredBy(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $entity->getTriggeredBy());
    }

    /**
     * Test that toPersistence() maps the startedAt timestamp.
     */
    public function testToPersistenceMapsStartedAt(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $this->assertEquals($domain->getStartedAt(), $entity->getStartedAt());
    }

    /**
     * Test that toPersistence() maps the completedAt timestamp.
     */
    public function testToPersistenceMapsCompletedAt(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $this->assertEquals($domain->getCompletedAt(), $entity->getCompletedAt());
    }
}
