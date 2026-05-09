<?php

/**
 * @file DeploymentProviderMapperToDomainTest.php
 *
 * Tests for DeploymentProviderMapper::toDomain().
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Mapper\DeploymentProviderMapper;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that toDomain() accurately converts a DeploymentProviderEntity to a domain DeploymentProvider.
 */
#[CoversClass(DeploymentProviderMapper::class)]
final class DeploymentProviderMapperToDomainTest extends DeploymentProviderMapperTest
{
    /**
     * Test that toDomain() returns a DeploymentProvider instance.
     */
    public function testToDomainReturnsDeploymentProvider(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertInstanceOf(DeploymentProvider::class, $domain);
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
     * Test that toDomain() maps the name.
     */
    public function testToDomainMapsName(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('Netlify Production', $domain->getName());
    }

    /**
     * Test that toDomain() maps the type enum.
     */
    public function testToDomainMapsType(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame(DeploymentProviderType::NETLIFY, $domain->getType());
    }

    /**
     * Test that toDomain() maps the URL.
     */
    public function testToDomainMapsUrl(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $domain->getUrl());
    }

    /**
     * Test that toDomain() maps the options array.
     */
    public function testToDomainMapsOptions(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertSame(['headers' => ['X-Secret' => 'abc']], $domain->getOptions());
    }

    /**
     * Test that toDomain() maps the isActive flag.
     */
    public function testToDomainMapsIsActive(): void
    {
        $domain = $this->class->toDomain($this->makeEntity());
        $this->assertTrue($domain->isActive());
    }

    /**
     * Test that toDomain() maps the createdAt timestamp.
     */
    public function testToDomainMapsCreatedAt(): void
    {
        $entity = $this->makeEntity();
        $domain = $this->class->toDomain($entity);
        $this->assertEquals($entity->getCreatedAt(), $domain->getCreatedAt());
    }

    /**
     * Test that toDomain() maps the updatedAt timestamp.
     */
    public function testToDomainMapsUpdatedAt(): void
    {
        $entity = $this->makeEntity();
        $domain = $this->class->toDomain($entity);
        $this->assertEquals($entity->getUpdatedAt(), $domain->getUpdatedAt());
    }

    /**
     * Test that toDomain() maps a null updatedAt.
     */
    public function testToDomainMapsNullUpdatedAt(): void
    {
        $entity = $this->makeEntity()->setUpdatedAt(null);
        $domain = $this->class->toDomain($entity);
        $this->assertNull($domain->getUpdatedAt());
    }
}
