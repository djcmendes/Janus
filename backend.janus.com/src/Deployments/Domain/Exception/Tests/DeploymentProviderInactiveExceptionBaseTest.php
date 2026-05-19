<?php

/**
 * @file DeploymentProviderInactiveExceptionBaseTest.php
 *
 * Construction and message tests for DeploymentProviderInactiveException.
 *
 * @package App\Deployments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception\Tests;

use App\Deployments\Domain\Exception\DeploymentProviderInactiveException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentProviderInactiveException message formatting and inheritance.
 */
#[CoversClass(className: DeploymentProviderInactiveException::class)]
final class DeploymentProviderInactiveExceptionBaseTest extends DeploymentProviderInactiveExceptionTest
{
    /**
     * Test that the SUT is an instance of DeploymentProviderInactiveException.
     */
    public function testIsInstanceOfDeploymentProviderInactiveException(): void
    {
        $this->assertInstanceOf(DeploymentProviderInactiveException::class, $this->class);
    }

    /**
     * Test that the exception extends RuntimeException.
     */
    public function testExtendsRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->class);
    }

    /**
     * Test that the exception message includes the provider ID.
     */
    public function testMessageContainsProviderId(): void
    {
        $this->assertStringContainsString(
            'aaaaaaaa-0000-7000-8000-000000000001',
            $this->class->getMessage(),
        );
    }

    /**
     * Test that the exception message matches the expected format.
     */
    public function testMessageMatchesExpectedFormat(): void
    {
        $this->assertSame(
            'Deployment provider "aaaaaaaa-0000-7000-8000-000000000001" is inactive.',
            $this->class->getMessage(),
        );
    }

    /**
     * Test that a different ID produces a correctly formatted message.
     */
    public function testMessageFormatsAnyId(): void
    {
        $e = new DeploymentProviderInactiveException('bbbbbbbb-0000-7000-8000-000000000002');
        $this->assertSame(
            'Deployment provider "bbbbbbbb-0000-7000-8000-000000000002" is inactive.',
            $e->getMessage(),
        );
    }
}
