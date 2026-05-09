<?php

/**
 * @file DeploymentNotFoundExceptionBaseTest.php
 *
 * Construction and message tests for DeploymentNotFoundException.
 *
 * @package App\Deployments\Domain\Exception\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Domain\Exception\Tests;

use App\Deployments\Domain\Exception\DeploymentNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies DeploymentNotFoundException message formatting and inheritance.
 */
#[CoversClass(DeploymentNotFoundException::class)]
final class DeploymentNotFoundExceptionBaseTest extends DeploymentNotFoundExceptionTest
{
    /**
     * Test that the SUT is an instance of DeploymentNotFoundException.
     */
    public function testIsInstanceOfDeploymentNotFoundException(): void
    {
        $this->assertInstanceOf(DeploymentNotFoundException::class, $this->class);
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
            'Deployment provider "aaaaaaaa-0000-7000-8000-000000000001" not found.',
            $this->class->getMessage(),
        );
    }

    /**
     * Test that a different ID produces a correctly formatted message.
     */
    public function testMessageFormatsAnyId(): void
    {
        $e = new DeploymentNotFoundException('bbbbbbbb-0000-7000-8000-000000000002');
        $this->assertSame(
            'Deployment provider "bbbbbbbb-0000-7000-8000-000000000002" not found.',
            $e->getMessage(),
        );
    }
}
