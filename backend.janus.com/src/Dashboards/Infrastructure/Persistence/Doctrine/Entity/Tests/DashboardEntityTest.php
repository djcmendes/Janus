<?php

/**
 * @file DashboardEntityTest.php
 *
 * Abstract base for DashboardEntity test suites.
 *
 * @package App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\Tests
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\Tests;

use App\Dashboards\Infrastructure\Persistence\Doctrine\Entity\DashboardEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Common setup, teardown, and factory helpers for DashboardEntity test suites.
 */
#[CoversClass(className: DashboardEntity::class)]
abstract class DashboardEntityTest extends TestCase
{
    /** @var DashboardEntity The system under test. */
    protected DashboardEntity $class;

    /**
     * Builds a fully-hydrated DashboardEntity before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->class = $this->makeEntity();
    }

    /**
     * Releases the SUT reference after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->class);
    }

    /**
     * Builds a fully-hydrated DashboardEntity with deterministic test values.
     *
     * @return DashboardEntity
     */
    protected function makeEntity(): DashboardEntity
    {
        return (new DashboardEntity())
            ->setId('aaaaaaaa-0000-7000-8000-000000000001')
            ->setName('Test Dashboard')
            ->setIcon('chart')
            ->setNote('Some note')
            ->setUserId('user-uuid-001')
            ->setCreatedAt(new \DateTimeImmutable('2024-01-01T00:00:00Z'))
            ->setUpdatedAt(new \DateTimeImmutable('2024-06-01T00:00:00Z'));
    }
}
