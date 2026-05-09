<?php

/**
 * @file DeploymentProviderBaseTest.php
 *
 * Constructor and interface compliance tests for the DeploymentProvider domain entity.
 *
 * @package App\Deployments\Domain\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Entity\Tests;

use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Constructor and interface compliance tests for the DeploymentProvider domain entity.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderBaseTest extends DeploymentProviderTest
{
    /**
     * Test that the SUT is an instance of DeploymentProvider.
     */
    public function testIsInstanceOfDeploymentProvider(): void
    {
        $this->assertInstanceOf(DeploymentProvider::class, $this->class);
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
     * Test that the constructor stores the name argument.
     */
    public function testConstructorSetsName(): void
    {
        $this->assertSame('Netlify Production', $this->class->getName());
    }

    /**
     * Test that the constructor stores the type argument.
     */
    public function testConstructorSetsType(): void
    {
        $this->assertSame(DeploymentProviderType::NETLIFY, $this->class->getType());
    }

    /**
     * Test that the constructor stores the url argument.
     */
    public function testConstructorSetsUrl(): void
    {
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $this->class->getUrl());
    }

    /**
     * Test that options defaults to null on construction.
     */
    public function testOptionsDefaultsToNull(): void
    {
        $this->assertNull($this->class->getOptions());
    }

    /**
     * Test that isActive defaults to true on construction.
     */
    public function testIsActiveDefaultsToTrue(): void
    {
        $this->assertTrue($this->class->isActive());
    }

    /**
     * Test that updatedAt defaults to null on construction.
     */
    public function testUpdatedAtDefaultsToNull(): void
    {
        $this->assertNull($this->class->getUpdatedAt());
    }

    /**
     * Test that createdAt is set to approximately the current time.
     */
    public function testConstructorSetsCreatedAtToNow(): void
    {
        $before = new \DateTimeImmutable();
        $p      = new DeploymentProvider('New', DeploymentProviderType::WEBHOOK, 'https://example.com');
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $p->getCreatedAt());
        $this->assertLessThanOrEqual($after, $p->getCreatedAt());
    }

    /**
     * Test that each instance receives a unique UUID.
     */
    public function testEachInstanceReceivesUniqueId(): void
    {
        $a = new DeploymentProvider('A', DeploymentProviderType::WEBHOOK, 'https://a.com');
        $b = new DeploymentProvider('B', DeploymentProviderType::VERCEL, 'https://b.com');

        $this->assertNotSame($a->getId(), $b->getId());
    }
}
