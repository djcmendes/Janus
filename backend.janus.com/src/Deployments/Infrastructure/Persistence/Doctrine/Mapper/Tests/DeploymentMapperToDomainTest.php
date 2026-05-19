<?php

/**
 * @file DeploymentMapperToDomainTest.php
 *
 * Tests for DeploymentMapper::toDomain().
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Entity\Deployment;
use App\Deployments\Domain\Enum\DeploymentRunStatus;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that toDomain() accurately converts a DeploymentEntity to a domain Deployment.
 */
#[CoversClass(className: DeploymentMapper::class)]
final class DeploymentMapperToDomainTest extends DeploymentMapperTest
{
    /**
     * Test that toDomain() returns a Deployment instance.
     */
    public function testToDomainReturnsDeployment(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertInstanceOf(Deployment::class, $domain);
    }

    /**
     * Test that toDomain() maps the UUID as a string.
     */
    public function testToDomainMapsId(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $domain->getId());
    }

    /**
     * Test that toDomain() maps the provider ID.
     */
    public function testToDomainMapsProviderId(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $domain->getProviderId());
    }

    /**
     * Test that toDomain() maps the status enum.
     */
    public function testToDomainMapsStatus(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame(DeploymentRunStatus::SUCCESS, $domain->getStatus());
    }

    /**
     * Test that toDomain() maps the log text.
     */
    public function testToDomainMapsLog(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('[HTTP 200] ok', $domain->getLog());
    }

    /**
     * Test that toDomain() maps a null log.
     */
    public function testToDomainMapsNullLog(): void
    {
        $entity = $this->makeEntity()->setLog(null);
        $domain = $this->class->toDomain($entity);
        $this->assertNull($domain->getLog());
    }

    /**
     * Test that toDomain() maps the triggeredBy UUID.
     */
    public function testToDomainMapsTriggeredBy(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $domain->getTriggeredBy());
    }

    /**
     * Test that toDomain() maps the startedAt timestamp.
     */
    public function testToDomainMapsStartedAt(): void
    {
        $entity = $this->makeEntity();
        $domain = $this->class->toDomain($entity);
        $this->assertEquals($entity->getStartedAt(), $domain->getStartedAt());
    }

    /**
     * Test that toDomain() maps the completedAt timestamp.
     */
    public function testToDomainMapsCompletedAt(): void
    {
        $entity = $this->makeEntity();
        $domain = $this->class->toDomain($entity);
        $this->assertEquals($entity->getCompletedAt(), $domain->getCompletedAt());
    }

    /**
     * Test that toDomain() maps a null completedAt.
     */
    public function testToDomainMapsNullCompletedAt(): void
    {
        $entity = $this->makeEntity()->setCompletedAt(null);
        $domain = $this->class->toDomain($entity);
        $this->assertNull($domain->getCompletedAt());
    }
}
