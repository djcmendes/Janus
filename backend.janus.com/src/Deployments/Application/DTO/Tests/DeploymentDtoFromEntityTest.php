<?php

/**
 * @file DeploymentDtoFromEntityTest.php
 *
 * Tests for DeploymentDto::fromEntity().
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that fromEntity() maps all Deployment domain entity fields to DTO properties.
 */
#[CoversClass(DeploymentDto::class)]
final class DeploymentDtoFromEntityTest extends DeploymentDtoTest
{
    /**
     * Test that fromEntity() returns a DeploymentDto instance.
     */
    public function testFromEntityReturnsDeploymentDto(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertInstanceOf(DeploymentDto::class, $dto);
    }

    /**
     * Test that fromEntity() maps the ID.
     */
    public function testFromEntityMapsId(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $dto->id);
    }

    /**
     * Test that fromEntity() maps the provider ID.
     */
    public function testFromEntityMapsProviderId(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $dto->providerId);
    }

    /**
     * Test that fromEntity() maps the status as its string value.
     */
    public function testFromEntityMapsStatus(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('success', $dto->status);
    }

    /**
     * Test that fromEntity() maps the log.
     */
    public function testFromEntityMapsLog(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('[HTTP 200] ok', $dto->log);
    }

    /**
     * Test that fromEntity() maps the triggeredBy UUID.
     */
    public function testFromEntityMapsTriggeredBy(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $dto->triggeredBy);
    }

    /**
     * Test that fromEntity() maps startedAt as an ISO-8601 string.
     */
    public function testFromEntityMapsStartedAt(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('2024-01-01T00:00:00+00:00', $dto->startedAt);
    }

    /**
     * Test that fromEntity() maps completedAt as an ISO-8601 string.
     */
    public function testFromEntityMapsCompletedAt(): void
    {
        $dto = DeploymentDto::fromEntity($this->makeDeployment());
        $this->assertSame('2024-01-01T00:01:00+00:00', $dto->completedAt);
    }

    /**
     * Test that fromEntity() maps a null completedAt.
     */
    public function testFromEntityMapsNullCompletedAt(): void
    {
        $deployment = \App\Deployments\Domain\Entity\Deployment::reconstitute(
            id:          'aaaaaaaa-0000-7000-8000-000000000001',
            providerId:  'pppppppp-0000-7000-8000-000000000001',
            status:      \App\Deployments\Domain\Enum\DeploymentRunStatus::RUNNING,
            log:         null,
            triggeredBy: null,
            startedAt:   new \DateTimeImmutable('2024-01-01T00:00:00Z'),
            completedAt: null,
        );
        $dto = DeploymentDto::fromEntity($deployment);
        $this->assertNull($dto->completedAt);
    }
}
