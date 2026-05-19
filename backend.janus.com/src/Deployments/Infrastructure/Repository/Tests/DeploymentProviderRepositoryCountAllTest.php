<?php

/**
 * @file DeploymentProviderRepositoryCountAllTest.php
 *
 * Tests for DeploymentProviderRepository::countAll().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentProviderRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that countAll() returns the correct integer count.
 */
#[CoversClass(className: DeploymentProviderRepository::class)]
final class DeploymentProviderRepositoryCountAllTest extends DeploymentProviderRepositoryTest
{
    /**
     * Test that countAll() returns the integer count from the query.
     */
    public function testCountAllReturnsInteger(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('7');

        $count = $this->class->countAll();

        $this->assertSame(7, $count);
    }

    /**
     * Test that countAll() returns zero when there are no records.
     */
    public function testCountAllReturnsZeroWhenEmpty(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('0');

        $count = $this->class->countAll();

        $this->assertSame(0, $count);
    }
}
