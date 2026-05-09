<?php

/**
 * @file DeploymentDtoBaseTest.php
 *
 * Constructor tests for DeploymentDto.
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentDto property storage via the constructor.
 */
#[CoversClass(DeploymentDto::class)]
final class DeploymentDtoBaseTest extends DeploymentDtoTest
{
    /**
     * Test that the SUT is an instance of DeploymentDto.
     */
    public function testIsInstanceOfDeploymentDto(): void
    {
        $this->assertInstanceOf(DeploymentDto::class, $this->class);
    }

    /**
     * Test that the id property is set correctly.
     */
    public function testIdIsSet(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->id);
    }

    /**
     * Test that the providerId property is set correctly.
     */
    public function testProviderIdIsSet(): void
    {
        $this->assertSame('pppppppp-0000-7000-8000-000000000001', $this->class->providerId);
    }

    /**
     * Test that the status property is set correctly.
     */
    public function testStatusIsSet(): void
    {
        $this->assertSame('success', $this->class->status);
    }

    /**
     * Test that the log property is set correctly.
     */
    public function testLogIsSet(): void
    {
        $this->assertSame('[HTTP 200] ok', $this->class->log);
    }

    /**
     * Test that the triggeredBy property is set correctly.
     */
    public function testTriggeredByIsSet(): void
    {
        $this->assertSame('uuuuuuuu-0000-7000-8000-000000000001', $this->class->triggeredBy);
    }

    /**
     * Test that the startedAt property is set correctly.
     */
    public function testStartedAtIsSet(): void
    {
        $this->assertSame('2024-01-01T00:00:00+00:00', $this->class->startedAt);
    }

    /**
     * Test that the completedAt property is set correctly.
     */
    public function testCompletedAtIsSet(): void
    {
        $this->assertSame('2024-01-01T00:01:00+00:00', $this->class->completedAt);
    }
}
