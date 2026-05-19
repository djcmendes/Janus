<?php

/**
 * @file DeploymentProviderEntityBaseTest.php
 *
 * Getter/setter compliance tests for the DeploymentProviderEntity Doctrine persistence model.
 *
 * @package App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Deployments\Domain\Enum\DeploymentProviderType;
use App\Deployments\Infrastructure\Persistence\Doctrine\Entity\DeploymentProviderEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Uid\Uuid;

/**
 * Getter/setter compliance tests for DeploymentProviderEntity.
 */
#[CoversClass(className: DeploymentProviderEntity::class)]
final class DeploymentProviderEntityBaseTest extends DeploymentProviderEntityTest
{
    /**
     * Test that the SUT is an instance of DeploymentProviderEntity.
     */
    public function testIsInstanceOfDeploymentProviderEntity(): void
    {
        $this->assertInstanceOf(DeploymentProviderEntity::class, $this->class);
    }

    /**
     * Test that getId() returns a Uuid instance.
     */
    public function testGetIdReturnsUuid(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->class->getId());
    }

    /**
     * Test that setId() stores the given UUID and returns fluent self.
     */
    public function testSetIdStoresAndReturnsSelf(): void
    {
        $uuid   = Uuid::fromString('bbbbbbbb-0000-7000-8000-000000000002');
        $result = $this->class->setId($uuid);
        $this->assertSame($uuid, $this->class->getId());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getName() returns the stored name.
     */
    public function testGetNameReturnsName(): void
    {
        $this->assertSame('Netlify Production', $this->class->getName());
    }

    /**
     * Test that setName() updates the name and returns fluent self.
     */
    public function testSetNameUpdatesAndReturnsSelf(): void
    {
        $result = $this->class->setName('Vercel Staging');
        $this->assertSame('Vercel Staging', $this->class->getName());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getType() returns the stored type enum.
     */
    public function testGetTypeReturnsType(): void
    {
        $this->assertSame(DeploymentProviderType::NETLIFY, $this->class->getType());
    }

    /**
     * Test that setType() updates the type and returns fluent self.
     */
    public function testSetTypeUpdatesAndReturnsSelf(): void
    {
        $result = $this->class->setType(DeploymentProviderType::VERCEL);
        $this->assertSame(DeploymentProviderType::VERCEL, $this->class->getType());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getUrl() returns the stored URL.
     */
    public function testGetUrlReturnsUrl(): void
    {
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $this->class->getUrl());
    }

    /**
     * Test that setUrl() updates the URL and returns fluent self.
     */
    public function testSetUrlUpdatesAndReturnsSelf(): void
    {
        $result = $this->class->setUrl('https://new-url.example.com');
        $this->assertSame('https://new-url.example.com', $this->class->getUrl());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getOptions() returns the stored options array.
     */
    public function testGetOptionsReturnsOptions(): void
    {
        $this->assertSame(['headers' => ['X-Secret' => 'abc']], $this->class->getOptions());
    }

    /**
     * Test that setOptions() accepts null and returns fluent self.
     */
    public function testSetOptionsAcceptsNullAndReturnsSelf(): void
    {
        $result = $this->class->setOptions(null);
        $this->assertNull($this->class->getOptions());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that isActive() returns true.
     */
    public function testIsActiveReturnsTrue(): void
    {
        $this->assertTrue($this->class->isActive());
    }

    /**
     * Test that setIsActive() updates the flag and returns fluent self.
     */
    public function testSetIsActiveUpdatesAndReturnsSelf(): void
    {
        $result = $this->class->setIsActive(false);
        $this->assertFalse($this->class->isActive());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getCreatedAt() returns a DateTimeImmutable.
     */
    public function testGetCreatedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getCreatedAt());
    }

    /**
     * Test that setCreatedAt() stores the timestamp and returns fluent self.
     */
    public function testSetCreatedAtStoresAndReturnsSelf(): void
    {
        $ts     = new \DateTimeImmutable('2025-01-01T00:00:00Z');
        $result = $this->class->setCreatedAt($ts);
        $this->assertSame($ts, $this->class->getCreatedAt());
        $this->assertSame($this->class, $result);
    }

    /**
     * Test that getUpdatedAt() returns a DateTimeImmutable.
     */
    public function testGetUpdatedAtReturnsDateTimeImmutable(): void
    {
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->class->getUpdatedAt());
    }

    /**
     * Test that setUpdatedAt() accepts null and returns fluent self.
     */
    public function testSetUpdatedAtAcceptsNullAndReturnsSelf(): void
    {
        $result = $this->class->setUpdatedAt(null);
        $this->assertNull($this->class->getUpdatedAt());
        $this->assertSame($this->class, $result);
    }
}
