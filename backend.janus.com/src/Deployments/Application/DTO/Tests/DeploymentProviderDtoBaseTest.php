<?php

/**
 * @file DeploymentProviderDtoBaseTest.php
 *
 * Constructor tests for DeploymentProviderDto.
 *
 * @package App\Deployments\Application\DTO\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Application\DTO\Tests;

use App\Deployments\Application\DTO\DeploymentProviderDto;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentProviderDto property storage via the constructor.
 */
#[CoversClass(className: DeploymentProviderDto::class)]
final class DeploymentProviderDtoBaseTest extends DeploymentProviderDtoTest
{
    /**
     * Test that the SUT is an instance of DeploymentProviderDto.
     */
    public function testIsInstanceOfDeploymentProviderDto(): void
    {
        $this->assertInstanceOf(DeploymentProviderDto::class, $this->class);
    }

    /**
     * Test that the id property is set correctly.
     */
    public function testIdIsSet(): void
    {
        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $this->class->id);
    }

    /**
     * Test that the name property is set correctly.
     */
    public function testNameIsSet(): void
    {
        $this->assertSame('Netlify Production', $this->class->name);
    }

    /**
     * Test that the type property is set correctly.
     */
    public function testTypeIsSet(): void
    {
        $this->assertSame('netlify', $this->class->type);
    }

    /**
     * Test that the url property is set correctly.
     */
    public function testUrlIsSet(): void
    {
        $this->assertSame('https://api.netlify.com/build_hooks/abc123', $this->class->url);
    }

    /**
     * Test that the options property is set correctly.
     */
    public function testOptionsIsSet(): void
    {
        $this->assertSame(['headers' => ['X-Secret' => 'abc']], $this->class->options);
    }

    /**
     * Test that the isActive property is set correctly.
     */
    public function testIsActiveIsSet(): void
    {
        $this->assertTrue($this->class->isActive);
    }

    /**
     * Test that the createdAt property is set correctly.
     */
    public function testCreatedAtIsSet(): void
    {
        $this->assertSame('2024-01-01T00:00:00+00:00', $this->class->createdAt);
    }

    /**
     * Test that the updatedAt property is set correctly.
     */
    public function testUpdatedAtIsSet(): void
    {
        $this->assertSame('2024-06-01T00:00:00+00:00', $this->class->updatedAt);
    }
}
