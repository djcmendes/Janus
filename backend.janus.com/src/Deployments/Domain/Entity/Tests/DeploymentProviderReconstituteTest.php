<?php

/**
 * @file DeploymentProviderReconstituteTest.php
 *
 * Tests for DeploymentProvider::reconstitute().
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
 * Verifies that reconstitute() restores a DeploymentProvider from persisted state.
 */
#[CoversClass(DeploymentProvider::class)]
final class DeploymentProviderReconstituteTest extends DeploymentProviderTest
{
    /**
     * Test that reconstitute() preserves the given ID.
     */
    public function testReconstitutePreservesId(): void
    {
        $p = $this->makeReconstituted(id: 'aaaaaaaa-0000-7000-8000-000000000001');
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $p->getId());
    }

    /**
     * Test that reconstitute() preserves the name.
     */
    public function testReconstitutePreservesName(): void
    {
        $p = $this->makeReconstituted(name: 'Vercel Staging');
        $this->assertSame('Vercel Staging', $p->getName());
    }

    /**
     * Test that reconstitute() preserves the type.
     */
    public function testReconstitutePreservesType(): void
    {
        $p = $this->makeReconstituted(type: DeploymentProviderType::VERCEL);
        $this->assertSame(DeploymentProviderType::VERCEL, $p->getType());
    }

    /**
     * Test that reconstitute() preserves the URL.
     */
    public function testReconstitutePreservesUrl(): void
    {
        $p = $this->makeReconstituted(url: 'https://vercel.com/api/deploy/abc');
        $this->assertSame('https://vercel.com/api/deploy/abc', $p->getUrl());
    }

    /**
     * Test that reconstitute() preserves the options array.
     */
    public function testReconstitutePreservesOptions(): void
    {
        $opts = ['headers' => ['Authorization' => 'Bearer token']];
        $p    = $this->makeReconstituted(options: $opts);
        $this->assertSame($opts, $p->getOptions());
    }

    /**
     * Test that reconstitute() preserves a null options value.
     */
    public function testReconstitutePreservesNullOptions(): void
    {
        $p = $this->makeReconstituted(options: null);
        $this->assertNull($p->getOptions());
    }

    /**
     * Test that reconstitute() preserves the isActive flag.
     */
    public function testReconstitutePreservesIsActive(): void
    {
        $p = $this->makeReconstituted(isActive: false);
        $this->assertFalse($p->isActive());
    }

    /**
     * Test that reconstitute() preserves the createdAt timestamp.
     */
    public function testReconstitutePreservesCreatedAt(): void
    {
        $ts = new \DateTimeImmutable('2023-01-01T00:00:00Z');
        $p  = $this->makeReconstituted(createdAt: $ts);
        $this->assertSame($ts, $p->getCreatedAt());
    }

    /**
     * Test that reconstitute() preserves a non-null updatedAt timestamp.
     */
    public function testReconstitutePreservesUpdatedAt(): void
    {
        $ts = new \DateTimeImmutable('2023-06-15T12:00:00Z');
        $p  = $this->makeReconstituted(updatedAt: $ts);
        $this->assertSame($ts, $p->getUpdatedAt());
    }

    /**
     * Test that reconstitute() returns a DeploymentProvider instance.
     */
    public function testReconstituteReturnsDeploymentProvider(): void
    {
        $this->assertInstanceOf(DeploymentProvider::class, $this->makeReconstituted());
    }
}
