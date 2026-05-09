<?php

/**
 * @file DeploymentRepositoryCountAllTest.php
 *
 * Tests for DeploymentRepository::countAll().
 *
 * @package App\Deployments\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Deployments\Infrastructure\Repository\Tests;

use App\Deployments\Infrastructure\Repository\DeploymentRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies that countAll() returns the correct integer count.
 */
#[CoversClass(DeploymentRepository::class)]
final class DeploymentRepositoryCountAllTest extends DeploymentRepositoryTest
{
    /**
     * Test that countAll() returns the integer count from the query.
     */
    public function testCountAllReturnsInteger(): void
    {
        $this->query->method('getSingleScalarResult')->willReturn('42');

        $count = $this->class->countAll();

        $this->assertSame(42, $count);
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

    /**
     * Test that countAll() applies providerId filter when given.
     */
    public function testCountAllAppliesProviderIdFilter(): void
    {
        $this->queryBuilder->expects($this->once())->method('andWhere');
        $this->query->method('getSingleScalarResult')->willReturn('5');

        $this->class->countAll('pppppppp-0000-7000-8000-000000000001');
    }
}
