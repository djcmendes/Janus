<?php

/**
 * @file DashboardRepositoryFindByIdTest.php
 *
 * Tests for DashboardRepository::findById().
 *
 * @package App\Dashboards\Infrastructure\Repository\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Repository\Tests;

use App\Dashboards\Domain\Entity\Dashboard;
use App\Dashboards\Infrastructure\Repository\DashboardRepository;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Verifies findById() maps a found DashboardEntity to a domain Dashboard and returns null when absent.
 */
#[CoversClass(className: DashboardRepository::class)]
final class DashboardRepositoryFindByIdTest extends DashboardRepositoryTest
{
    /**
     * Test that findById() returns a Dashboard domain entity when found.
     */
    public function testFindByIdReturnsDashboardWhenFound(): void
    {
        $entity = $this->makeDashboardEntity();
        $this->entityManager->method('find')->willReturn($entity);

        $result = $this->class->findById('aaaaaaaa-0000-7000-8000-000000000001');

        $this->assertInstanceOf(Dashboard::class, $result);
    }

    /**
     * Test that findById() maps the entity ID to the domain Dashboard ID.
     */
    public function testFindByIdMapsId(): void
    {
        $entity = $this->makeDashboardEntity();
        $this->entityManager->method('find')->willReturn($entity);

        $result = $this->class->findById('aaaaaaaa-0000-7000-8000-000000000001');

        $this->assertSame('aaaaaaaa-0000-7000-8000-000000000001', $result->getId());
    }

    /**
     * Test that findById() returns null when the entity is not found.
     */
    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $result = $this->class->findById('non-existent-id');

        $this->assertNull($result);
    }
}
