<?php

/**
 * @file DeploymentProviderDtoFromEntityTest.php
 *
 * Tests for DeploymentProviderDto::fromEntity().
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use App\Deployments\Domain\Entity\DeploymentProvider;
use App\Deployments\Domain\Enum\DeploymentProviderType;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that fromEntity() maps all DeploymentProvider domain entity fields to DTO properties.
 */
#[CoversClass(DeploymentProviderDto::class)]
final class DeploymentProviderDtoFromEntityTest extends DeploymentProviderDtoTest
{
    /**
     * Test that fromEntity() returns a DeploymentProviderDto instance.
     */
    public function testFromEntityReturnsDeploymentProviderDto(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertInstanceOf(DeploymentProviderDto::class, $dto);
    }

    /**
     * Test that fromEntity() maps the ID.
     */
    public function testFromEntityMapsId(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $dto->id);
    }

    /**
     * Test that fromEntity() maps the name.
     */
    public function testFromEntityMapsName(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('Netlify Production', $dto->name);
    }

    /**
     * Test that fromEntity() maps the type as its string value.
     */
    public function testFromEntityMapsType(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('netlify', $dto->type);
    }

    /**
     * Test that fromEntity() maps the URL.
     */
    public function testFromEntityMapsUrl(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $dto->url);
    }

    /**
     * Test that fromEntity() maps the options array.
     */
    public function testFromEntityMapsOptions(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame(['headers' => ['X-Secret' => 'abc']], $dto->options);
    }

    /**
     * Test that fromEntity() maps the isActive flag.
     */
    public function testFromEntityMapsIsActive(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertTrue($dto->isActive);
    }

    /**
     * Test that fromEntity() maps createdAt as an ISO-8601 string.
     */
    public function testFromEntityMapsCreatedAt(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('2024-01-01T00:00:00+00:00', $dto->createdAt);
    }

    /**
     * Test that fromEntity() maps updatedAt as an ISO-8601 string.
     */
    public function testFromEntityMapsUpdatedAt(): void
    {
        $dto = DeploymentProviderDto::fromEntity($this->makeProvider());
        $this->assertSame('2024-06-01T00:00:00+00:00', $dto->updatedAt);
    }

    /**
     * Test that fromEntity() maps a null updatedAt.
     */
    public function testFromEntityMapsNullUpdatedAt(): void
    {
        $provider = DeploymentProvider::reconstitute(
            id:        'aaaaaaaa-0000-7000-8000-000000000001',
            name:      'Test',
            type:      DeploymentProviderType::WEBHOOK,
            url:       'https://example.com',
            options:   null,
            isActive:  true,
            createdAt: new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: null,
        );
        $dto = DeploymentProviderDto::fromEntity($provider);
        $this->assertNull($dto->updatedAt);
    }
}
