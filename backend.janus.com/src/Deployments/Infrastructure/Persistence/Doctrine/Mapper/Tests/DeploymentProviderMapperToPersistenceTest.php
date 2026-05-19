<?php

/**
 * @file DeploymentProviderMapperToPersistenceTest.php
 *
 * Tests for DeploymentProviderMapper::toPersistence().
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Verifies that toPersistence() accurately converts a domain DeploymentProvider to a DeploymentProviderEntity.
 */
#[CoversClass(className: DeploymentProviderMapper::class)]
final class DeploymentProviderMapperToPersistenceTest extends DeploymentProviderMapperTest
{
    /**
     * Test that toPersistence() returns a DeploymentProviderEntity instance.
     */
    public function testToPersistenceReturnsDeploymentProviderEntity(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertInstanceOf(DeploymentProviderEntity::class, $entity);
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
     * Test that toPersistence() maps the name.
     */
    public function testToPersistenceMapsName(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame('Netlify Production', $entity->getName());
    }

    /**
     * Test that toPersistence() maps the type enum.
     */
    public function testToPersistenceMapsType(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame(DeploymentProviderType::NETLIFY, $entity->getType());
    }

    /**
     * Test that toPersistence() maps the URL.
     */
    public function testToPersistenceMapsUrl(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $entity->getUrl());
    }

    /**
     * Test that toPersistence() maps the options array.
     */
    public function testToPersistenceMapsOptions(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertSame(['headers' => ['X-Secret' => 'abc']], $entity->getOptions());
    }

    /**
     * Test that toPersistence() maps the isActive flag.
     */
    public function testToPersistenceMapsIsActive(): void
    {
        $entity = $this->class->toPersistence($this->makeDomain());
        $this->assertTrue($entity->isActive());
    }

    /**
     * Test that toPersistence() maps the createdAt timestamp.
     */
    public function testToPersistenceMapsCreatedAt(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $this->assertEquals($domain->getCreatedAt(), $entity->getCreatedAt());
    }

    /**
     * Test that toPersistence() maps the updatedAt timestamp.
     */
    public function testToPersistenceMapsUpdatedAt(): void
    {
        $domain = $this->makeDomain();
        $entity = $this->class->toPersistence($domain);
        $this->assertEquals($domain->getUpdatedAt(), $entity->getUpdatedAt());
    }
}
